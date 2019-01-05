<?php

Yii::import('ext.phpthumb.PhpThumbFactory');
Yii::import('application.modules.store.components.StoreImagesConfig');
Yii::import('application.modules.store.components.StoreUploadedImage');

/**
 * Class StoreProductImageSaver
 *
 * Save/handle uploaded product images
 */
class StoreProductImageSaver
{
	/**
	 * @var StoreProductImage
	 */
	protected $imageModel;

	/**
	 * @param StoreProduct $product
	 * @param CUploadedFile $image
	 */
	public function __construct(StoreProduct $product, CUploadedFile $image)
	{
		$name     = StoreUploadedImage::createName($product, $image);
		$fullPath = StoreUploadedImage::getSavePath().'/'.$name;
		$image->saveAs($fullPath);
		@chmod($fullPath, 0777);

		// Check if product has main image
		$is_main = (int)StoreProductImage::model()->countByAttributes(array(
			'product_id' => $product->id,
			'is_main'    => 1
		)) === 0;

		$this->imageModel = new StoreProductImage;
		$this->imageModel->product_id         = $product->id;
		$this->imageModel->name               = $name;
		$this->imageModel->is_main            = $is_main;
		$this->imageModel->uploaded_by        = Yii::app()->user->getId();
		$this->imageModel->date_uploaded      = date('Y-m-d H:i:s');
		$this->imageModel->original_file_name = $image->getName();
		$this->imageModel->save(false);

		$this->resize($fullPath);
		$this->watermark($fullPath);
	}

	public function getModel()
	{
		return $this->imageModel;
	}

	/**
	 * Resize uploaded image if sizes bigger defined in settings table
	 *
	 * @param $fullPath string
	 */
	public function resize($fullPath)
	{
		$thumb  = PhpThumbFactory::create($fullPath);
		$sizes  = StoreImagesConfig::get('maximum_image_size');
		$method = StoreImagesConfig::get('resizeMethod');
		$thumb->$method($sizes[0], $sizes[0])->save($fullPath);
	}

	/**
	 * Draws watermark on image.
	 *
	 * @param $fullPath string to image
	 */
	public function watermark($fullPath)
	{
		// Add watermark;
		if(StoreImagesConfig::get('watermark_active'))
		{
			$pic = PhpThumbFactory::create($fullPath);
			$pos = StoreImagesConfig::get('watermark_position_vertical').StoreImagesConfig::get('watermark_position_horizontal');

			try {
				$watermark = PhpThumbFactory::create(Yii::getPathOfAlias('webroot.uploads') . '/watermark.png');

				$pic->addWatermark(
					$watermark,
					$pos,
					StoreImagesConfig::get('watermark_opacity'),
					0,
					0
				);
				$pic->save($fullPath);
			} catch(Exception $e) {
				// pass
			}
		}
	}
}