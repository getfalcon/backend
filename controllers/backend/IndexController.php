<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\controllers\backend;

use falcon\backend\models\login\LoginForm;
use Yii;
use falcon\backend\app\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class IndexController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {

        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionIndex() {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $this->layout = '//_clear';
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack(Url::to(['/admin']));
        } else {
            return $this->render('index', ['model' => $model]);
        }
    }
}