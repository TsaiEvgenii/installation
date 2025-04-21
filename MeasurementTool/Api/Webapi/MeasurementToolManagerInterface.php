<?php

namespace BelVG\MeasurementTool\Api\Webapi;

interface MeasurementToolManagerInterface
{
    /**
     * @param int $customerId
     * @param int $measurementToolId
     *
     * @return bool
     */
    public function removeMeasurementTool(int $customerId, int $measurementToolId): bool;
}
