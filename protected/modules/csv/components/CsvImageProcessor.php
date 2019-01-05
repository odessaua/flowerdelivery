<?php

Yii::import('application.modules.csv.components.CsvImage');

/**
 * Class CsvImageProcessor
 * Saves main and additional product images.
 */
class CsvImageProcessor
{
	protected $importer;
	protected $product;
	protected $data;

	/**
	 * @param $importer
	 * @param $product
	 * @param array $data array row from csv file
	 */
	public function __construct(CsvImporter $importer, StoreProduct $product, array $data)
	{
		$this->importer = $importer;
		$this->product  = $product;
		$this->data     = $data;
	}

	public function save()
	{
		$this->saveMainImage();
		$this->saveAdditionalImages();
	}

	protected function saveMainImage()
	{
		if($this->dataHas('image') && !$this->product->mainImage)
			$this->addImageToProduct($this->data['image'], true);
	}

	protected function saveAdditionalImages()
	{
		$key = 'additional_images';
		if(!$this->dataHas($key))
			return;

		$images = explode(',', $this->data[$key]);
		$images = array_map('trim', $images);

		foreach($images as $image)
		{
			if(!StoreProductImage::countByProductAndOriginalName($this->product->id, $image))
				$this->addImageToProduct($image);
		}
	}

	/**
	 * @param $imageName string filename or url
	 * @param $main bool make image main
	 */
	protected function addImageToProduct($imageName, $main=false)
	{
		$image = CsvImage::create($imageName);

		if($image)
		{
			$storeProductModel = $this->product->addImage($image);

			if($main)
				$storeProductModel->makeMain();

			if($this->importer->deleteDownloadedImages)
				$image->deleteDownloadedFile();
		}
	}

	protected function dataHas($key)
	{
		return isset($this->data[$key]) && !empty($this->data[$key]);
	}
}
