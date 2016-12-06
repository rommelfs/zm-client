<?php
namespace MonarcFO\Service;

use \MonarcCore\Model\Entity\Scale;
use \MonarcCore\Model\Entity\Object;

/**
 * ANR Cartography Risks Real & Targeted Service
 *
 * Class AnrCartoRiskService
 * @package MonarcFO\Service
 */
class AnrCartoRiskService extends \MonarcCore\Service\AbstractService
{
    protected $anrTable;
    protected $instanceTable;
    protected $instanceRiskTable;
    protected $instanceConsequenceTable;
    protected $threatTable;

    protected $filterColumns = [];

    protected $dependencies = [];

    private $anr = null;
    private $listScales = null;
    private $headers = null;

    public function getCartoReal($anrId){
        $this->buildListScalesAndHeaders($anrId);

        list($counters, $distrib) = $this->getCountersRisks('raw');
        return [
            'Impact' => $this->listScales[Scale::TYPE_IMPACT],
            'MxV' => $this->headers,
            'counters' => $counters,
            'distrib' => $distrib,
        ];
    }

    public function getCartoTargeted($anrId){
        $this->buildListScalesAndHeaders($anrId);

        if($this->anr->get('evalRisks')){
            list($counters, $distrib) = $this->getCountersRisks('target');
            return [
                'Impact' => $this->listScales[Scale::TYPE_IMPACT],
                'MxV' => $this->headers,
                'counters' => $counters,
                'distrib' => $distrib,
            ];
        }else{
            return null;
        }
    }

    private function buildListScalesAndHeaders($anrId){
        if(!$this->anr || $this->anr->get('id') != $anrId){
            $this->anr = $this->get('anrTable')->getEntity($anrId);
        }
        if(is_null($this->listScales)){
            $scales = $this->get('table')->getEntityByFields(['anr'=>$this->anr->get('id')]);
            $this->listScales = [
                Scale::TYPE_IMPACT => [],
                Scale::TYPE_THREAT => [],
                Scale::TYPE_VULNERABILITY => [],
            ];
            foreach($scales as $scale){
                if(isset($this->listScales[$scale->get('type')])){
                    for($i = $scale->get('min'); $i <= $scale->get('max'); $i++){
                        $this->listScales[$scale->get('type')][$i] = $i;
                    }
                }
            }
        }
        if(is_null($this->headers)){
            $this->headers = [];
            foreach($this->listScales[Scale::TYPE_IMPACT] as $i){
                foreach($this->listScales[Scale::TYPE_THREAT] as $m){
                    foreach($this->listScales[Scale::TYPE_VULNERABILITY] as $v){
                        $val = -1;
                        if($i != -1 && $m != -1 && $v != -1){
                            $val = $m * $v;
                        }
                        $this->headers[$val] = $val;
                    }   
                }
            }
            asort($this->headers);
        }
    }

    private function getCountersRisks($mode = 'raw'){
        // On croise avec les données des risques
        $changeField = $mode == 'raw' ? 'ir.cacheMaxRisk' : 'ir.cacheTargetedRisk';
        $query = $this->get('instanceRiskTable')->getRepository()->createQueryBuilder('ir');
        $result = $query->select([
                'ir.id', 'IDENTITY(ir.asset)', 'IDENTITY(ir.threat)', 'IDENTITY(ir.vulnerability)', $changeField.' as maximus',
                'i.c as ic', 'i.i as ii', 'i.d as id', 'IDENTITY(i.object)',
                'm.c as mc', 'm.i as mi', 'm.d as md',
                'o.scope',
            ])->where('ir.anr = :anrid')
            ->setParameter(':anrid',$this->anr->get('id'))
            ->andWhere($changeField." != -1")
            ->innerJoin('ir.instance', 'i')
            ->innerJoin('ir.threat','m')
            ->innerJoin('i.object','o')->getQuery()->getResult();

        $counters = $distrib = $temp = [];
        foreach($result as $r){
            //on détermine le contexte de travail
            //A. Quel est l'impact MAX au regard du masque CID de la menace
            $imax = $c = $i = $d = 0;
            if($r['mc']) $c = $r['ic'];
            if($r['mi']) $i = $r['ii'];
            if($r['md']) $d = $r['id'];

            $imax = max($c, $i, $d);
            $max = $r['maximus'];
            $right = $imax > 0 ? round($max / $imax) : 0;

            $context = [
                'impact'    => $imax,
                'right'     => $right,
                'amv'       => $r['asset'].';'.$r['threat'].';'.$r['vulnerability'],
                'max'       => $max,
                'color'     => $this->getColor($max),
            ];

            //on est obligé de faire l'algo en deux passes pour pouvoir compter les objets globaux qu'une seule fois
            if($r['scope'] == Object::SCOPE_GLOBAL){
                if(!isset($temp[$r['object']][$r['asset'].';'.$r['threat'].';'.$r['vulnerability']][0])){ // dans ce cas pas grand chose à faire on doit stocker le context local
                    $temp[$r['object']][$r['asset'].';'.$r['threat'].';'.$r['vulnerability']][0] = $context;
                }else{ // dans ce cas on doit comparer la valeur max qu'on a. Si c'est plus haut alors on remplace par le contexte courant
                    $cur = $temp[$r['object']][$r['asset'].';'.$r['threat'].';'.$r['vulnerability']][0];
                    if($r['maximus'] > $cur['max'] ){//on doit remplacer $cur
                        unset($temp[$r['object']][$r['asset'].';'.$r['threat'].';'.$r['vulnerability']][0]);
                        $temp[$r['object']][$r['asset'].';'.$r['threat'].';'.$r['vulnerability']][0] = $context;
                    } // sinon rien à faire
                }
            }else{ // pour les locaux, l'amv peut exister plusieurs fois sur le même biblio, du coup pour bien les compter plusieurs fois on rajoute
                $temp[$r['object']][$r['asset'].';'.$r['threat'].';'.$r['vulnerability']][$r['id']] = $context;
            }
        }

        // le premier algo nous a permis d'isoler les maximus des globaux pour ne les compter qu'une seule fois
        // maintenant il faut compter de manière à pouvoir distribuer cela dans la matrice
        foreach($temp as $id_biblio => $risks){
            foreach($risks as $amv => $contexts){
                foreach($contexts as $idx => $context){
                    if(! isset($counters[$context['impact']][$context['right']]) ){
                        $counters[$context['impact']][$context['right']] = 0;
                    }

                    if(!isset($distrib[$context['color']])){
                         $distrib[$context['color']] = 0;
                    }
                    $counters[$context['impact']][$context['right']] ++;
                    $distrib[$context['color']] ++;
                }
            }
        }

        return [$counters, $distrib];
    }

    /*
    Provient de l'ancienne version, on ne remonte que les valeurs '' / 0 / 1 / 2, les couleurs seront traitées par le FE
    */
    private function getColor($val){
        if($val == -1 || is_null($val)) return '';
        if($val <= $this->anr->get('seuilRolf1')) return 0;
        if($val <= $this->anr->get('seuilRolf2')) return 1;
        return 2;
    }
}