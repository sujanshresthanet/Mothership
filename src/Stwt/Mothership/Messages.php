<?php namespace Stwt\Mothership;

use Illuminate\Support\Facades\Session as Session;

class Messages
{
     public static $msgss = array();

      /**
       * Add a message to the message array (adds to the user's session)
       *
       * @param string  $type You can have several types of messages, these are class names 
       *                      for  Bootstrap's messaging classes, usually, info, error, 
       *                      success, warning
       * @param string $message  The message you want to add to the list
       */
    public static function add($type = 'info', $message = false)
    {
        if (!$message) {
            return false;
        }
        if (is_array($message)) {
            foreach ($message as $msg) {
                static::$msgss[] = [$type => $msg];
            }
        } else {
            static::$msgss[] = [$type => $message];
        }
        Session::flash('messages', static::$msgss);
    }

    /**
    * Pull back those messages from the session
    * @return array
    */
    public static function get()
    {
        return Session::get('messages');
    }

    /**
    * Gets all the messages from the session and formats them accordingly for Twitter bootstrap.
    * @return string
    */
    public static function getHtml()
    {
        $messages = Session::get('messages');
        error_log(print_r($messages, 1));
        $output = '';
        if ($messages) {
            foreach ($messages as $t => $m) {
                $output .= '<div class="alert alert-'.$t.'"><a class="close" data-dismiss="alert">×</a>'.$m;
                $output .= '</div>';
            }
        }
        return $output;
    }
}
