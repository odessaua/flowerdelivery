<?php

class CitySeoController extends SAdminController
{
    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate($city_id = 0, $lang_id = 0)
    {
        $this->actionUpdate(true, $city_id, $lang_id);
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($new = false, $city_id = 0, $lang_id = 0)
    {
        // проверка, есть ли уже запись с такими параметрами (комбинация $city_id и $lang_id) в БД
        $check = CitySeo::model()->findByAttributes(array('city_id' => $_GET['city_id'], 'lang_id' => $_GET['lang_id']));
        if(!$check){
            $new = true; // такой записи нет – создаём новую
        }

        if ($new === true)
        {
            $model=new CitySeo;
            if(!empty($city_id)) $model->city_id = (int)$city_id;
            if(!empty($lang_id)) $model->lang_id = (int)$lang_id;
            //$model->unsetAttributes();
        }
        else
        {
            $model = CitySeo::model()->findByAttributes(array('city_id' => $_GET['city_id'], 'lang_id' => $_GET['lang_id']));
        }

        if (!$model)
            throw new CHttpException(404, Yii::t('StoreModule.admin', 'Регион доставки не найден.'));


        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        $form = new STabbedForm('application.modules.store.views.admin.citySeo._form', $model);
        if(isset($_POST['CitySeo']))
        {
            $model->attributes=$_POST['CitySeo'];
            if($model->validate())
            {
                $model->save();
                if (isset($_POST['REDIRECT']))
                    $this->smartRedirect($model);
                else
                    $this->redirect(array('index'));


                $this->setFlashMessage(Yii::t('StoreModule.admin', 'Изменения успешно сохранены'));
                $this->redirect(array('index'));

                // 

            }

        }

        $this->render('update',array(
            'model'=>$model,
            'form'=>$form
        ));
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $model=new City('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['City']))
            $model->attributes=$_GET['City'];
        //$model = CitySeo::model()->with('translate')->findAll();

        $dataProvider = $model->language(1)->search();
        $dataProvider->pagination->pageSize = Yii::app()->settings->get('core', 'productsPerPageAdmin');
        $dataProvider->sort->attributes->name = 'translate.name';
        /*$dataProvider=new CActiveDataProvider('City', array(
            'criteria'=>array(
//                'condition'=>'status=1',
                //'order'=>'t.id asc',
                'with'=>array('translate'),
            ),
            'countCriteria'=>array(
                //'condition'=>'status=1',
                // 'order' and 'with' clauses have no meaning for the count query
            ),
            'pagination'=>array(
                'pageSize'=>Yii::app()->settings->get('core', 'productsPerPageAdmin'),
            ),
        ));*/

        $this->render('admin',array(
            'model'=>$model,
            'dataProvider'=>$dataProvider,
        ));
    }

    /**
     * Delete method
     * @param array $id
     */
    public function actionDelete($id = array())
    {
        if (Yii::app()->request->isPostRequest)
        {
            $model = CitySeo::model()->findAllByPk($_REQUEST['id']);

            if (!empty($model))
            {
                foreach($model as $m)
                {
                    /*if($m->countProducts() == 0)*/
                    $m->delete();
                    /*else
                        throw new CHttpException(409, Yii::t('OrdersModule.admin','Ошибка удаления способа доставки. Он используется заказами.'));*/
                }
            }

            if (!Yii::app()->request->isAjaxRequest)
                $this->redirect('index');
        }
    }
}
