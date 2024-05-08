<?php

namespace app\components;

use app\models\User;
use yii\rbac\CheckAccessInterface;
use Yii;

class AccessChecker implements CheckAccessInterface
{

    public function checkAccess($userId, $permissionName, $params = []) {
        if (strpos($permissionName, ',')) {
            $names = explode(',', $permissionName);
            $result = false;
            foreach ($names as $name) {
                $result |= $this->checkAccess($userId, $name, $params);
            }
            return $result;
        }
        if ($permissionName == '*') {
            return true;
        }
        if ($permissionName == '@') {
            return !Yii::$app->user->isGuest;
        }
        if (!Yii::$app->user->isGuest && Yii::$app->user->id == $userId) {
            $user = Yii::$app->user->identity;
        } else {
            $user = User::findOne(['id' => $userId]);
        }
        if (empty($user)) {
            return $permissionName == '?';
        }
        switch ($user->role) {
            case User::ROLE_ADMIN:
                return true;
            case User::ROLE_MODERATOR:
                return $permissionName == User::ROLE_MODERATOR || (is_array($user->permissions) && in_array($permissionName, $user->permissions));
            case User::ROLE_USER:
            default:
                return false;
        }
    }

}
