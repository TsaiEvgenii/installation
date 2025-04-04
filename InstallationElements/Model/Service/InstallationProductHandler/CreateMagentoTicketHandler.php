<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Model\Service\InstallationProductHandler;


use Aheadworks\Helpdesk\Api\Data\TicketFlatInterfaceFactory;
use Aheadworks\Helpdesk\Api\Data\TicketInterface;
use Aheadworks\Helpdesk\Api\Data\TicketInterfaceFactory;
use Aheadworks\Helpdesk\Api\TicketFlatRepositoryInterface;
use Aheadworks\Helpdesk\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk\Model\ResourceModel\ThreadMessage as ThreadMessageResource;
use Aheadworks\Helpdesk\Model\Source\Ticket\Status;
use Aheadworks\Helpdesk\Model\ThreadMessage;
use Aheadworks\Helpdesk\Model\ThreadMessageFactory;
use BelVG\InstallationElements\Api\Service\InstallationProductHandler\HandlerInterface;
use BelVG\InstallationElements\Model\ResourceModel\InstallationOrderTicket as InstallationOrderTicketResource;
use BelVG\InstallationElements\Model\Service\GetInstallationItemFromOrder;
use BelVG\InstallationElements\Model\Service\GetOrderQty;
use BelVG\InstallationElements\Model\Service\Ticket\DepartmentHelper;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\Sales\Api\Data\OrderInterface;
use BelVG\InstallationElements\Model\InstallationOrderTicketFactory as InstallationOrderTicketFactory;
use Magento\Store\Model\StoreManagerInterface;

class CreateMagentoTicketHandler implements HandlerInterface
{
    public function __construct(
        protected InstallationOrderTicketFactory $installationOrderTicketFactory,
        protected InstallationOrderTicketResource $installationOrderTicketResource,
        protected TicketInterfaceFactory $ticketDataFactory,
        protected StoreManagerInterface $storeManager,
        protected TicketRepositoryInterface $ticketRepository,
        protected ThreadMessageFactory $threadMessageFactory,
        protected ThreadMessageResource $threadMessageResource,
        protected TicketFlatRepositoryInterface $ticketFlatRepository,
        protected TicketFlatInterfaceFactory $ticketFlatDataFactory,
        protected GetOrderQty $getOrderQtyService,
        protected DateTimeFactory $dateFactory,
        protected DepartmentHelper $departmentHelper,
        protected GetInstallationItemFromOrder $getInstallationItemFromOrderService,
    ){}

    public function isAvailable(OrderInterface $order): bool
    {
        $installationOrderTicketModel = $this->installationOrderTicketFactory->create();
        $this->installationOrderTicketResource->load($installationOrderTicketModel, $order->getEntityId(), 'order_id');

        return !$installationOrderTicketModel->getId();
    }

    /**
     * @throws AlreadyExistsException
     */
    public function execute(OrderInterface $order)
    {
        $ticket = $this->createTicket($order);
        $this->createTicketMessage($ticket, $order);
        $this->createTicketFlat($ticket);

        $installationOrderTicketModel = $this->installationOrderTicketFactory->create();
        $installationOrderTicketModel->setData(
            [
                'ticket_id' => $ticket->getId(),
                'order_id' => $order->getEntityId()
            ]
        );
        $this->installationOrderTicketResource->save($installationOrderTicketModel);
    }

    protected function createTicket(OrderInterface $order): TicketInterface
    {
        $ticket = $this->ticketDataFactory->create();
        $ticket->setOrderId($order->getEntityId());
        $ticket->setCustomerEmail($order->getCustomerEmail());
        $ticket->setCustomerId($order->getCustomerId());
        $ticket->setStatus(Status::OPEN_VALUE);
        $ticket->setDepartmentId($this->departmentHelper->getDepartmentId());
        $ticket->setUid($order->getIncrementId());
        $ticket->setSubject(
            (string)__('New Installation request from %customerEmail on %storeName', [
                'customerEmail' => $order->getCustomerEmail(),
                'storeName' => $this->storeManager->getStore()->getName()
            ])
        );
        $savedTicket = $this->ticketRepository->save($ticket);
        $order->getExtensionAttributes()->setInstallationTicketId($savedTicket->getId());

        return $savedTicket;
    }
    /**
     * @throws AlreadyExistsException
     */
    private function createTicketMessage(
        TicketInterface $ticket,
        OrderInterface $order
    ): void {
        /** @var ThreadMessage $threadMessage */
        $threadMessage = $this->threadMessageFactory->create();
        $threadMessage
            ->setTicketId($ticket->getId())
            ->setContent($this->getMessageFromOrder($order))
            ->setType(ThreadMessage::OWNER_CUSTOMER_VALUE)
            ->setAuthorName($order->getCustomerFirstname() . ' ' . $order->getCustomerLastname())
            ->setAuthorEmail($order->getCustomerEmail())
            ->setCreatedAt($this->dateFactory->create()->gmtDate());

        $this->threadMessageResource->save($threadMessage);
    }

    private function getMessageFromOrder(OrderInterface $order): string
    {
        $address = $order->getBillingAddress();
        $street = $address->getStreet() ?? '';
        if (is_array($street)) {
            $street = implode(' ', $street);
        }
        $messageParts = [
            __('Firstname:') . ' ' . $order->getCustomerFirstname(),
            __('Lastname:') . ' ' . $order->getCustomerLastname(),
            __('Email:') . ' ' . $order->getCustomerEmail(),
            __('Phone:') . ' ' . $address->getTelephone(),
            __('Address:') . ' ' . $street,
            __('Postcode:') . ' ' . $address->getPostcode(),
            __('City:') . ' ' . $address->getCity(),
            '<hr>',
            __('Antal:') . ' ' . $this->getOrderQtyService->get($order)
        ];

        $additionalOptionsText = $this->getInstallationItemFromOrderService->getItemAdditionalOptionsText($order);
        $messageParts = [
            ...$messageParts,
            ...$additionalOptionsText
        ];


        return implode(chr(10), $messageParts);
    }
    /**
     * @throws LocalizedException
     */
    protected function createTicketFlat(TicketInterface $ticket): void
    {
        try {
            $ticketFlat = $this->ticketFlatRepository->getByTicketId($ticket->getId());
        } catch (\Exception $e) {
            $ticketFlat = $this->ticketFlatDataFactory->create();
        }

        $ticketFlat->setData('order_id', $ticket->getOrderId());
        $ticketFlat->setData('agent_id', $ticket->getAgentId());
        $ticketFlat->setTicketId($ticket->getId());

        $this->ticketFlatRepository->save($ticketFlat);
    }
}