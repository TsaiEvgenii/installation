<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Controller\Index;


use BelVG\MeasurementTool\Api\ElementRepositoryInterface;
use BelVG\MeasurementTool\Api\MeasurementToolRepositoryInterface;
use BelVG\MeasurementTool\Api\RoomRepositoryInterface;
use BelVG\MeasurementTool\Api\Data\ElementInterface;
use BelVG\MeasurementTool\Api\Data\ElementInterfaceFactory;
use BelVG\MeasurementTool\Api\Data\MeasurementToolInterface;
use BelVG\MeasurementTool\Api\Data\MeasurementToolInterfaceFactory;
use BelVG\MeasurementTool\Api\Data\RoomInterfaceFactory;
use BelVG\MeasurementTool\Api\Data\RoomInterface;
use BelVG\MeasurementTool\Model\Service\PrepareImgToSave;
use BelVG\MeasurementTool\Model\Service\SaveMeasurementToolImg;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;

class Save extends \Magento\Framework\App\Action\Action
{
    private const LOG_PREFIX = '[BelVG_MeasurementTool::SaveController]: ';

    public function __construct(
        protected MeasurementToolRepositoryInterface $measurementToolRepository,
        protected MeasurementToolInterfaceFactory $measurementToolFactory,
        protected RoomRepositoryInterface $roomRepository,
        protected RoomInterfaceFactory $roomFactory,
        protected ElementRepositoryInterface $elementRepository,
        protected ElementInterfaceFactory $elementFactory,
        protected PrepareImgToSave $prepareImgToSaveService,
        protected SaveMeasurementToolImg $saveMeasurementToolImgService,
        protected Session $customerSession,
        protected LoggerInterface $logger,
        Context $context
    )
    {
        parent::__construct($context);
    }

    public function execute()
    {
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        try {
            $oldMeasurementToolImages = [];
            $measurementToolId = (int)$this->getRequest()->getParam('entity_id');
            $measurementToolName = $this->getRequest()->getParam('name');
            $rooms = $this->getRequest()->getParam('rooms') ?? [];
            if (count($rooms) === 0) {
                $this->messageManager->addErrorMessage(__('No one room was set'));
                if ($measurementToolId) {
                    return $redirect->setPath('*/*/', ['measurement_tool_id' => $measurementToolId]);
                } else {
                    return $redirect->setPath('*/*/');
                }
            }
            $measurementToolDescription = $this->getRequest()->getParam('description');
            /** @var MeasurementToolInterface $measurementToolDataModel */
            $measurementToolDataModel = $this->measurementToolFactory->create();

            if ($measurementToolId) {
                $measurementToolDataModel->setEntityId($measurementToolId);

                //Remove old rooms and elements
                $oldMeasurementDataModel = $this->measurementToolRepository->getById($measurementToolId);
                $oldMeasurementToolImages = $oldMeasurementDataModel->getImages();
                $oldRooms = $oldMeasurementDataModel->getRooms();
                foreach ($oldRooms as $oldRoom){
                    $currentElements = [];
                    $roomDeleteFlag = true;
                    $oldRoomEntityId = $oldRoom->getEntityId();
                    foreach ($rooms as $room){
                        if((int)$room['entity_id'] === $oldRoomEntityId){
                            $roomDeleteFlag = false;
                            $currentElements = $room['elements'];
                            break;
                        }
                    }

                    if ($roomDeleteFlag === true) {
                        $this->roomRepository->delete($oldRoom);
                        continue;
                    }
                    $oldElements = $oldRoom->getElements();
                    foreach ($oldElements as $oldElement){
                        $elementDeleteFlag = true;
                        $oldElementEntityId = $oldElement->getEntityId();
                        foreach ($currentElements as $currentElement){
                            if((int)$currentElement['entity_id'] === $oldElementEntityId){
                                $elementDeleteFlag = false;
                            }
                        }
                        if($elementDeleteFlag === true){
                            $this->elementRepository->delete($oldElement);
                        }
                    }

                }
            }


            $measurementToolDataModel->setName($measurementToolName);
            $measurementToolDataModel->setDescription($measurementToolDescription);
            $measurementToolDataModel->setCustomerId((int)$this->customerSession->getCustomerId());
            $measurementToolDataModel = $this->measurementToolRepository->save($measurementToolDataModel);

            //Save images
            $measurementToolImages = $this->getRequest()->getParam('images') ?? [];
            $this->saveMeasurementToolImgService->save($measurementToolImages, $measurementToolDataModel, $oldMeasurementToolImages);


            foreach ($rooms as $room) {
                /** @var RoomInterface $roomDataModel */
                $roomDataModel = $this->roomFactory->create();
                if ($room['entity_id']) {
                    $roomDataModel->setEntityId($room['entity_id']);
                }
                $roomDataModel->setName($room['name'] ?? '');
                $roomDataModel->setRecordId((int)($room['record_id'] ?? 0));
                $roomDataModel->setMeasurementToolId($measurementToolDataModel->getEntityId());
                $roomDataModel = $this->roomRepository->save($roomDataModel);
                $roomElems = $room['elements'] ?? [];
                foreach ($roomElems as $roomElem){
                    /** @var ElementInterface $elemDataModel */
                    $elemDataModel = $this->elementFactory->create();
                    if ($roomElem['entity_id']) {
                        $elemDataModel->setEntityId($roomElem['entity_id']);
                    }
                    $elemDataModel->setName($roomElem['name'] ?? '');
                    $elemDataModel->setType($roomElem['type']);
                    $elemDataModel->setWidth((float)$roomElem['width']);
                    $elemDataModel->setHeight((float)$roomElem['height']);
                    $elemDataModel->setQty((int)$roomElem['qty']);
                    $elemDataModel->setRecordId((int)($roomElem['record_id'] ?? 0));
                    $elemDataModel->setRoomId($roomDataModel->getEntityId());
                    if ($imgPath = $this->prepareImgToSaveService->prepare($roomElem['img'] ?? [])) {
                        $elemDataModel->setImg($imgPath);
                    } elseif (($roomElem['img'][0]['url'] ?? false) === false) {
                        $elemDataModel->setImg('');
                    }
                    $this->elementRepository->save($elemDataModel);
                }
            }
            $this->messageManager->addSuccessMessage(__('Measurement Tool "%1" was created', $measurementToolDataModel->getName()));

            return $redirect->setPath('*/*/', ['measurement_tool_id' => $measurementToolDataModel->getEntityId()]);

        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
            $this->messageManager->addErrorMessage(__('Something went wrong while creating the Measurement Tool.'));
        }

        return $redirect->setPath('*/*/');
    }
}