<?php namespace VojtaSvoboda\SmartEmailing\Components;

use ApplicationException;
use Cms\Classes\ComponentBase;
use Request;
use SmartEmailing\v3\Api;
use SmartEmailing\v3\Exceptions\RequestException;
use SmartEmailing\v3\Request\Import\Campaign;
use SmartEmailing\v3\Request\Import\Contact;
use SmartEmailing\v3\Request\Import\ContactList;
use SmartEmailing\v3\Request\Import\DoubleOptInSettings;
use SmartEmailing\v3\Request\Send\SenderCredentials;
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
            'double-opt-in-email-id' => [
                'title' => 'Double opt-in email ID',
                'description' => 'In SmartEmailing > Campaigns > E-mails open requested E-mail and use numeric ID in URL, probably something like 1.',
                'type' => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'Double opt-in email list ID has to be numeric',
            ],
            'double-opt-in-sender-name' => [
                'title' => 'Double opt-in sender name',
                'description' => 'Name of opt-in campaign',
                'type' => 'string',
            ],
            'double-opt-in-sender-email' => [
                'title' => 'Double opt-in sender email',
                'description' => 'Must be a confirmed email',
                'type' => 'string',
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

        // prepare double opt-in settings
        $doubleOptIn = null;
        $doubleOptInEmailId = $this->property('double-opt-in-email-id');
        $doubleOptInSenderName = $this->property('double-opt-in-sender-name');
        $doubleOptInSenderEmail = $this->property('double-opt-in-sender-email');
        if (!empty($doubleOptInEmailId) && !empty($doubleOptInSenderName) && !empty($doubleOptInSenderEmail)) {
            $sender = new SenderCredentials();
            $sender->setFrom($doubleOptInSenderEmail);
            $sender->setSenderName($doubleOptInSenderName);
            $sender->setReplyTo($doubleOptInSenderEmail);
            $campaign = new Campaign($doubleOptInEmailId, $sender);
            $doubleOptIn = new DoubleOptInSettings($campaign);
        }

        // import contact
        $import = $smartEmailing->import()->addContact($contact);
        $import
            ->settings()
            ->setPreserveUnSubscribed(false)
            ->setDoubleOptInSettings($doubleOptIn);

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
