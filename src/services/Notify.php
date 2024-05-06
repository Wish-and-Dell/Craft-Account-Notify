<?php

namespace wishanddell\accountnotify\services;

use yii\base\Component;
use Craft;
use craft\elements\User;

class Notify extends Component
{
    /**
     * Check if a user account has had changes
     */
    public function check($user)
    {
        // Get the user before these changes
        $original = User::find()->id($user->id)->one();

        if (!$original)
            return;
        
        // Get list of fields that have changed
        $diff = $this->diff($original, $user);
        if (empty($diff))
            return;
        
        // Send the email
        $view = \Craft::$app->getView();         
        if ($view->getTemplateMode() !== $view::TEMPLATE_MODE_SITE) {
            $view->setTemplateMode($view::TEMPLATE_MODE_SITE);
        }

        $oldMode = \Craft::$app->view->getTemplateMode();
        
        try {
            // Attempt to load contact template from local project
            \Craft::$app->view->setTemplateMode(\Craft::$app->view::TEMPLATE_MODE_SITE);
            $html = Craft::$app->getView()->renderTemplate("_accountnotify", ['diff' => $diff]);
        } catch(\Exception $e) {
            // If not found, load the default one supplied by this plugin
            \Craft::$app->view->setTemplateMode(\Craft::$app->view::TEMPLATE_MODE_CP);
            $html = Craft::$app->getView()->renderTemplate("/accountnotify/_email", ['diff' => $diff]);
        }

        Craft::$app
            ->getMailer()
            ->compose()
            ->setTo($original->email)
            ->setSubject('Account details updated')
            ->setHtmlBody($html)
            ->send();

        \Craft::$app->view->setTemplateMode($oldMode);
    }
    
    /**
     * Gets the differences between the original user object and the new one
     */
    protected function diff($original, $user)
    {
        $result = [];

        $fields = array_merge(['username','password','email','fullName'], array_keys($user->fieldValues));
        foreach($fields AS $field) {
            if ($user->$field != $original->$field) {
                $result[$field] = [
                    'was' => $original->$field,
                    'now' => $user->$field
                ];
            }
        }

        return $result;
    }
}