<?php

namespace Cogensoft\Sitemap\Block\Sitemap;

use Magento\Framework\View\Element\Context;
use Magento\Sitemap\Helper\Data as SitemapHelper;
use Magento\Sitemap\Model\ResourceModel\Sitemap\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\StoreResolver;

class Robots extends \Magento\Sitemap\Block\Robots
{
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

	/**
	 * @var \Magento\Sitemap\Helper\Data
	 */
	protected $_sitemapHelper;

	public function __construct(
		Context $context,
		StoreResolver $storeResolver,
		CollectionFactory $sitemapCollectionFactory,
		SitemapHelper $sitemapHelper,
		StoreManagerInterface $storeManager,
		array $data = []
	) {
		$this->_storeManager = $storeManager;
		$this->_sitemapHelper = $sitemapHelper;

		parent::__construct(
			$context,
			$storeResolver,
			$sitemapCollectionFactory,
			$sitemapHelper,
			$storeManager,
			$data
		);
	}

	/** Sitemap is not site specific - https://github.com/magento/magento2/issues/28901 */
	protected function _toHtml()
	{
		/** @var \Magento\Store\Model\Website $website */
		$website =  $this->_storeManager->getWebsite($this->_storeManager->getStore()->getWebsiteId());;

		$storeIds = [];
		foreach ($website->getStoreIds() as $storeId) {
			if ((bool)$this->_sitemapHelper->getEnableSubmissionRobots($storeId)) {
				$storeIds[] = (int)$storeId;
			}
		}

		$links = [];
		if ($storeIds) {
			$links = array_merge($links, $this->getSitemapLinks($storeIds));
		}

		return $links ? implode(PHP_EOL, $links) . PHP_EOL : '';
	}
}