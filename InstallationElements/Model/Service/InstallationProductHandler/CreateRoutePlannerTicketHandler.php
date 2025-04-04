<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Service\InstallationProductHandler;


use Aheadworks\Helpdesk\Api\TicketRepositoryInterface;
use BelVG\InstallationElements\Api\Service\InstallationProductHandler\HandlerInterface;
use BelVG\InstallationElements\Model\Service\Config;
use BelVG\InstallationElements\Model\Service\GetInstallationItemFromOrder;
use BelVG\InstallationElements\Model\Service\GetOrderQty;
use BelVG\RoutePlanner\Model\ApiClient;
use BelVG\RoutePlanner\Model\Service\PrepareDataService;
use BelVG\RoutePlanner\Model\Service\Request\Task\ToDTOConverter;
use BelVG\RoutePlanner\Model\Service\Response\CreateTaskResponseHandler;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use function Aws\strip_fips_pseudo_regions;

class CreateRoutePlannerTicketHandler implements HandlerInterface
{
    private const LOG_PREFIX = '[BelVG_InstallationElements::CreateRouteplannerTicketHandler]: ';

    public function __construct(
        protected Config $installationElementsConfig,
        protected PrepareDataService $prepareDataService,
        protected TicketRepositoryInterface $ticketRepository,
        protected StoreManagerInterface $storeManager,
        protected DirectoryHelper $directoryHelper,
        protected GetOrderQty $getOrderQtyService,
        protected ToDTOConverter $toDoConverter,
        protected ApiClient $apiClient,
        protected CreateTaskResponseHandler $createTaskResponseHandler,
        protected GetInstallationItemFromOrder $getInstallationItemFromOrderService,
        protected Emulation $emulation,
        protected LoggerInterface $logger
    ){}
    public function isAvailable(OrderInterface $order): bool
    {
        return (bool)$order->getExtensionAttributes()->getInstallationTicketId();
    }

    /**
     * @throws \Exception
     */
    public function execute(OrderInterface $order)
    {
        $storeId = $order->getStoreId();
        $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);
        $ticketId = $order->getExtensionAttributes()->getInstallationTicketId();

        $data = $this->prepareDataService->prepareData([
            'task_info'       => $this->getTaskInfo($order),
            'customer_info'   => $this->getCustomerInfo($order),
            'additional_info' => $this->getAdditionalInfo($order),
        ]);

        $rpRequest = $this->toDoConverter->convert($data);
        $response = $this->apiClient->setTask($rpRequest);

        $responseHandleResult = $this->createTaskResponseHandler->handle((int)$ticketId, $response);
        if ($responseHandleResult['adminMessageType']) {
            $responseType = $responseHandleResult['adminMessageType'];
            $loggerMessage = sprintf(
                self::LOG_PREFIX . ' %s',
                $responseHandleResult['adminMessage']
            );
            switch ($responseType) {
                case 'error':
                case 'warning':
                    $this->logger->$responseType($loggerMessage);
                    break;
                default:
                    $this->logger->info($loggerMessage);
            }
        }

        $this->emulation->stopEnvironmentEmulation();
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    protected function getTaskInfo(OrderInterface $order): array
    {
        $routPlannerSettings = $this->installationElementsConfig->getRouteplannerSettings((int)$order->getStoreId());
        $ticketId = $order->getExtensionAttributes()->getInstallationTicketId();
        $awTicket = $this->ticketRepository->getById($ticketId);
        return [
            'ticket_id'           => (int)$ticketId,
            'task_id'             => $awTicket->getUid(),
            'status'              => (string)$routPlannerSettings['status_after_creation'],
            'task_name'           => $awTicket->getUid(),
            'description'         => $this->installationElementsConfig->getBelVGHelpdeskType(),
            'task_type'           => (string)$routPlannerSettings['type_id'],
            'belvg_helpdesk_type' =>  $this->installationElementsConfig->getBelVGHelpdeskType(),
            'task_notes'          => $this->getNote($order),
        ];
    }

    protected function getCustomerInfo(OrderInterface $order): array
    {
        $address = $order->getShippingAddress();
        $street = $address->getStreet() ?? '';
        if (is_array($street)) {
            $street = implode(' ', $street);
        }

        return [
            'customer_name'    => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
            'customer_address' => $street,
            'customer_city'    => $address->getCity(),
            'customer_zipcode' => $address->getPostcode(),
            'customer_country' => $address->getCountryId(),
            'customer_email'   => $order->getCustomerEmail(),
            'customer_phone'   => $address->getTelephone(),
        ];
    }

    protected function getAdditionalInfo(OrderInterface $order): array
    {
        $additionalInfo = [
            'order_increment_id' => $order->getIncrementId(),
        ];
        $additionalInfo['task_notes'] = $this->getNote($order);

        return $additionalInfo;
    }

    protected function getNote(OrderInterface $order):string
    {
        $taskNotes = [
            __('Quantity:'). ' ' . $this->getOrderQtyService->get($order)
        ];
        $additionalOptions = $this->getInstallationItemFromOrderService->getItemAdditionalOptionsText($order);
        $taskNotes = [
            ...$taskNotes,
            ...$additionalOptions
        ];
        return  implode(chr(10), $taskNotes);
    }
}