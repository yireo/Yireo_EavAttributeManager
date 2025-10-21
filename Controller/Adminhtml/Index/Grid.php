<?php declare(strict_types=1);

namespace Yireo\EavAttributeManager\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory as ResultPageFactory;

class Grid extends Action
{
    const ADMIN_RESOURCE = 'Yireo_EavAttributeManager::grid';

    /**
     * @param Context $context
     * @param ResultPageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        private ResultPageFactory $resultPageFactory,
    ) {
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return Page
     * @throws \Exception
     */
    public function execute(): Page
    {
        $title = 'Yireo EAV Attribute Manager';

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getLayout()->getUpdate()->addHandle('yireo_eav_attribute_manager_grid');
        $resultPage->setActiveMenu('Yireo_EavAttributeManager::index');
        $resultPage->addBreadcrumb(__($title), __($title));
        $resultPage->getConfig()->getTitle()->prepend(__($title));

        return $resultPage;
    }
}
