<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcFO\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use MonarcCore\Model\Entity\AbstractEntity;

/**
 * Recommandation Historic
 *
 * @ORM\Table(name="recommandations_historics")
 * @ORM\Entity
 */
class RecommandationHistoric extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \MonarcFO\Model\Entity\Anr
     *
     * @ORM\ManyToOne(targetEntity="MonarcFO\Model\Entity\Anr", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="anr_id", referencedColumnName="id", nullable=true)
     * })
     */
    protected $anr;

    /**
     * @var \MonarcFO\Model\Entity\InstanceRisk
     *
     * @ORM\ManyToOne(targetEntity="MonarcFO\Model\Entity\InstanceRisk", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="instance_risk_id", referencedColumnName="id", nullable=true)
     * })
     */
    protected $instanceRisk;

    /**
     * @var smallint
     *
     * @ORM\Column(name="final", type="smallint", options={"unsigned":false, "default":1})
     */
    protected $final = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="impl_comment", type="string", length=255, nullable=true)
     */
    protected $implComment;

    /**
     * @var string
     *
     * @ORM\Column(name="reco_code", type="string", length=100, nullable=true)
     */
    protected $recoCode;

    /**
     * @var string
     *
     * @ORM\Column(name="reco_description", type="string", length=255, nullable=true)
     */
    protected $recoDescription;

    /**
     * @var smallint
     *
     * @ORM\Column(name="reco_importance", type="smallint", options={"unsigned":true, "default":1})
     */
    protected $recoImportance = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="reco_comment", type="string", length=255, nullable=true)
     */
    protected $recoComment;

    /**
     * @var string
     *
     * @ORM\Column(name="reco_responsable", type="string", length=255, nullable=true)
     */
    protected $recoResponsable;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="reco_duedate", type="datetime", nullable=true)
     */
    protected $recoDuedate;

    /**
     * @var string
     *
     * @ORM\Column(name="risk_instance", type="string", length=255, nullable=true)
     */
    protected $riskInstance;

    /**
     * @var string
     *
     * @ORM\Column(name="risk_instance_context", type="string", length=255, nullable=true)
     */
    protected $riskInstanceContext;

    /**
     * @var string
     *
     * @ORM\Column(name="risk_asset", type="string", length=255, nullable=true)
     */
    protected $riskAsset;

    /**
     * @var string
     *
     * @ORM\Column(name="risk_threat", type="string", length=255, nullable=true)
     */
    protected $riskThreat;

    /**
     * @var smallint
     *
     * @ORM\Column(name="risk_threat_val", type="smallint", options={"unsigned":false, "default":-1})
     */
    protected $riskThreatVal = -1;

    /**
     * @var string
     *
     * @ORM\Column(name="risk_vul", type="string", length=255, nullable=true)
     */
    protected $riskVul;

    /**
     * @var smallint
     *
     * @ORM\Column(name="risk_vul_val_before", type="smallint", options={"unsigned":false, "default":-1})
     */
    protected $riskVulValBefore = -1;

    /**
     * @var smallint
     *
     * @ORM\Column(name="risk_vul_val_after", type="smallint", options={"unsigned":false, "default":-1})
     */
    protected $riskVulValAfter = -1;

    /**
     * @var smallint
     *
     * @ORM\Column(name="risk_kind_of_measure", type="smallint", options={"unsigned":false, "default":-1})
     */
    protected $riskKindOfMeasure = -1;

    /**
     * @var string
     *
     * @ORM\Column(name="risk_comment_before", type="string", length=255, nullable=true)
     */
    protected $riskCommentBefore;

    /**
     * @var string
     *
     * @ORM\Column(name="risk_comment_after", type="string", length=255, nullable=true)
     */
    protected $riskCommentAfter;

    /**
     * @var smallint
     *
     * @ORM\Column(name="risk_max_risk_before", type="smallint", options={"unsigned":false, "default":-1})
     */
    protected $riskMaxRiskBefore = -1;

    /**
     * @var string
     *
     * @ORM\Column(name="risk_color_before", type="string", length=100, nullable=true)
     */
    protected $riskColorBefore;

    /**
     * @var smallint
     *
     * @ORM\Column(name="risk_max_risk_after", type="smallint", options={"unsigned":false, "default":-1})
     */
    protected $riskMaxRiskAfter = -1;

    /**
     * @var string
     *
     * @ORM\Column(name="risk_color_after", type="string", length=100, nullable=true)
     */
    protected $riskColorAfter;

    /**
     * @var string
     *
     * @ORM\Column(name="cache_comment_after", type="string", length=255, nullable=true)
     */
    protected $cacheCommentAfter;

    /**
     * @var string
     *
     * @ORM\Column(name="creator", type="string", length=255, nullable=true)
     */
    protected $creator;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="updater", type="string", length=255, nullable=true)
     */
    protected $updater;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Asset
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Anr
     */
    public function getAnr()
    {
        return $this->anr;
    }

    /**
     * @param Anr $anr
     * @return Scale
     */
    public function setAnr($anr)
    {
        $this->anr = $anr;
        return $this;
    }
}