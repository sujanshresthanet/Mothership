<?php namespace Stwt\Mothership;

use Illuminate\Support\Facades\Auth as Auth;
use Illuminate\Support\Facades\View as View;
use User;

class HomeController extends BaseController
{
    /**
     * Render a form to update user profiles
     * 
     * @return View
     */
    public function getProfile()
    {
        $data = [];

        $userId = Auth::user()->id;
        $user   = User::find($userId);

        $form = FormGenerator::resource($user)
            ->method('put')
            ->form();

        $formAttr = [
            'class'     => 'form-horizontal',
            'method'    => 'POST',
        ];

        $form->attr($formAttr);

        $data['title'] = 'Your Profile';
        $data['content'] = $form->generate();

        return View::make('mothership::home.index')
            ->with($data)
            ->with($this->getTemplateData());
    }
}
