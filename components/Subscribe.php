<?php namespace VojtaSvoboda\SmartEmailing\Components;

use ApplicationException;
use Cms\Classes\ComponentBase;
use Request;
use SmartEmailing\v3\Api;
use SmartEmailing\v3\Exceptions\RequestException;
use SmartEmailing\v3\Request\Import\Contact;
use SmartEmailing\v3\Request\Import\ContactList;
use ValidationException;
use Validator;
use VojtaSvoboda\SmartEmailing\Models\Settings;

class Subscribe extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'SmartEmailing subscribe',
            'description' => 'SmartEmailing subscribe form',
        ];
    }

    public function defineProperties()
    {
        return [
            'list' => [
                'title' => 'SmartEmailing list ID',
                'description' => 'In SmartEmailing > Contacts open requested List and use numeric ID in URL, probably something like 1.',
                'type' => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'SmartEmailing list ID has to be numeric',
            ],
        ];
    }

    public function onSignup()
    {
        // prepare API credentials
        $api_username = Settings::get('api_username');
        $api_key = Settings::get('api_key');
        if (empty($api_key) || empty($api_username)) {
            throw new ApplicationException('SmartEmailing API username or key are not configured.');
        }

        // fetch input
        $data = Request::post();

        // validate input
        $validation = Validator::make($data, [
            'email' => 'required|email|min:2|max:64',
        ]);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        // add new subscriber
        $smartEmailing = new Api($api_username, $api_key);
        $this->page['error'] = null;

        // create new contact
        $contact = new Contact($data['email']);
        $contact->contactList()->create($this->property('list'), ContactList::CONFIRMED);

        // import contact
        $import = $smartEmailing->import()->addContact($contact);
        $import->settings()->setPreserveUnSubscribed(false);

        $status = null;
        try {
            $result = $import->send();
            $status = $result->statusCode();
        } catch (RequestException $exception) {
            $this->page['error'] = 'Something went wrong, try it again later.';
        }

        if ($status !== 201) {
            $this->page['error'] = 'Something went wrong, try it again later.';
        }
    }
}
