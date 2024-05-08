<?php

namespace app\controllers;

use app\models\forms\LoginForm;
use app\models\forms\PasswordResetRequestForm;
use app\models\forms\ResendVerificationEmailForm;
use app\models\forms\SignupForm;
use app\models\forms\VerifyEmailForm;
use app\models\Page;
use app\models\forms\ResetPasswordForm;
use app\models\User;
use Yii;
use yii\base\InvalidArgumentException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class SiteController extends Controller
{

    public function actions() {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    public function behaviors() {
        return [
            'access' => [
                'class'        => AccessControl::class,
                'only'         => ['login', 'logout', 'signup'],
                'rules'        => [
                    [
                        'actions' => ['signup'],
                        'allow'   => true,
                        'roles'   => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
                'denyCallback' => function ($user) {
                    $this->response->redirect(['/']);
                },
            ],
        ];
    }

    public function actionIndex() {
        if(Yii::$app->user->isGuest){
            return $this->redirect(['/site/login']);
        }else{
            return $this->redirect(['/pm/default/index']);
        }

        $page = Page::findOne(['slug' => 'index', 'status' => Page::STATUS_ACTIVE]);

        return $page ? $this->render('//page/show', ['page' => $page, 'category' => null, 'search' => null]) : $this->renderContent('');
    }

    /**
     * Logs in a user.
     *
     * @param null $ret
     *
     * @return mixed
     */
    public function actionLogin($ret = null) {
        if(!Yii::$app->user->isGuest){
            if($ret){
                return $this->redirect($ret);
            }
            return $this->redirect(['/pm/default/index']);
        }
        $model = new LoginForm();
        if($model->load(Yii::$app->request->post()) && $model->login()){
            if($ret){
                return $this->redirect($ret);
            }
            return $this->redirect(['/pm']);
        }
        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout() {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Signs user up.
     *
     * @param mixed|null $slug
     *
     * @return mixed
     */
    public function actionSignup($slug = null) {
        $model = new SignupForm();
        $referrer = null;

        if($slug && $slug !== 'signup'){
            $referrer = User::findByUsername($slug, null);
            if(empty($referrer)){
                Yii::$app->session->addFlash('error', Yii::t('site', 'Referrer not found'));
            }else{
                $model->referrer = $referrer->username;
            }
        }

        $id = $referrer ? $referrer->id : 0;
        Yii::$app->session->set('referrer', $id);
        $model->referrer_id = $id = $id ?: null;
        if($model->load(Yii::$app->request->post()) && $model->signup()){
            Yii::$app->session->addFlash('success', Yii::t('site', 'Thank you for registration. Please check your inbox for verification email.'));
            return $this->goHome();
        }
        return $this->render('signup', [
            'model'    => $model,
            'id'       => $id,
            'referrer' => $referrer,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset() {
        $model = new PasswordResetRequestForm();
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            if($model->sendEmail()){
                Yii::$app->session->addFlash('success', Yii::t('site', 'Check your email {email} for further instructions.', ['email' => $model->email]));
                return $this->goHome();
            }
            Yii::$app->session->addFlash('error', Yii::t('site', 'Sorry, we are unable to reset password for the provided email address.'));
        }
        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     *
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token) {
        try{
            $model = new ResetPasswordForm($token);
        }catch(InvalidArgumentException $e){
            throw new BadRequestHttpException($e->getMessage());
        }
        if($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()){
            Yii::$app->session->addFlash('success', 'New password saved.');

            return $this->goHome();
        }
        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     *
     * @return yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionVerifyEmail($token) {
        try{
            $model = new VerifyEmailForm($token);
        }catch(InvalidArgumentException $e){
            throw new BadRequestHttpException($e->getMessage());
        }
        if(($user = $model->verifyEmail()) && Yii::$app->user->login($user)){
            Yii::$app->session->addFlash('success', Yii::t('site', 'Your email has been confirmed!'));
            return $this->goHome();
        }
        Yii::$app->session->addFlash('error', Yii::t('site', 'Sorry, we are unable to verify your account with provided token.'));
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail() {
        $model = new ResendVerificationEmailForm();
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            if($model->sendEmail()){
                Yii::$app->session->addFlash('success', Yii::t('site', 'Check your email for further instructions.'));
                return $this->goHome();
            }
            Yii::$app->session->addFlash('error', Yii::t('site', 'Sorry, we are unable to resend verification email for the provided email address.'));
        }
        return $this->render('resendVerificationEmail', [
            'model' => $model,
        ]);
    }
}