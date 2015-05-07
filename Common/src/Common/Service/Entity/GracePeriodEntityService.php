<?php
/**
 * GracePeriodEntityService.php
 */
namespace Common\Service\Entity;

/**
 * Class GracePeriodEntityService
 *
 * Handle grace period records for licences.
 *
 * @package Common\Service\Entity
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class GracePeriodEntityService extends AbstractEntityService
{
    /**
     * The entity.
     *
     * @var string The entity to reference in the backend.
     */
    protected $entity = 'GracePeriod';

    /**
     * Get all grace periods for a licence.
     *
     * @param null $licenceId The licence id.
     *
     * @return mixed The grace periods.
     */
    public function getGracePeriodsForLicence($licenceId = null)
    {
        $query = array(
            'licence' => $licenceId,
            'sort' => 'startDate',
            'order' => 'ASC'
        );

        return $this->getList($query);
    }
}
