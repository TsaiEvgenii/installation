<?php

namespace BelVG\MeasurementTool\Model\Service;

use BelVG\MeasurementTool\Api\Webapi\MeasurementToolManagerInterface;
use BelVG\MeasurementTool\Api\MeasurementToolRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

class MeasurementToolManager implements MeasurementToolManagerInterface
{
    private const LOG_PREFIX = '[BelVG_MeasurementTool::MeasurementElementManagerService]: ';
    protected MeasurementToolRepositoryInterface $measurementToolRepository;

    public function __construct(
        MeasurementToolRepositoryInterface $measurementToolRepository
    ){
        $this->measurementToolRepository = $measurementToolRepository;
    }

    /**
     * @param int $customerId
     * @param int $measurementToolId
     *
     * @return bool
     * @throws LocalizedException
     */
    public function removeMeasurementTool(int $customerId, int $measurementToolId): bool
    {
        $measurementTool = $this->measurementToolRepository->getById($measurementToolId);
        return $this->measurementToolRepository->delete($measurementTool);
    }


}
