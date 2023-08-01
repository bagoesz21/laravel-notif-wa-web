<?php

namespace Bagoesz21\LaravelNotifWaWeb\Endpoints;

use Bagoesz21\LaravelNotifWaWeb\Endpoints\BaseEndpoint;

class GroupEndpoint extends BaseEndpoint
{
    protected $config = [
        'uri' => 'group'
    ];

    public function create()
    {
        $payloads = [
            'id' => $this->phoneLink,
        ];

        return $this->makeRequest('create', $payloads, 'POST');
    }

    public function leave($groupId)
    {
        $payloads = [
            'id' => $groupId
        ];

        return $this->makeRequest('leave', $payloads, 'GET');
    }

    public function getAll()
    {
        return $this->makeRequest('listall', [], 'GET');
    }

    public function inviteUser($groupId, $userId)
    {
        $payloads = [
            'id' => $groupId,
            'users' => $userId
        ];

        return $this->makeRequest('inviteuser', $payloads, 'POST');
    }

    public function makeAdmin($groupId, $userId)
    {
        $payloads = [
            'id' => $groupId,
            'users' => $userId
        ];

        return $this->makeRequest('makeadmin', $payloads, 'POST');
    }

    public function demoteadmin($groupId, $userId)
    {
        $payloads = [
            'id' => $groupId,
            'users' => $userId
        ];

        return $this->makeRequest('demoteadmin', $payloads, 'POST');
    }

    public function getGroupInviteCode($groupId)
    {
        $payloads = [
            'id' => $groupId,
        ];

        return $this->makeRequest('getinvitecode', $payloads, 'GET');
    }

    public function getAllGroup($groupId)
    {
        return $this->makeRequest('getallgroups', [], 'GET');
    }

    public function updateGroupParticipant($groupId, $userId, $action)
    {
        $payloads = [
            'id' => $groupId,
            'users' => $userId,
            'action' => $action
        ];

        return $this->makeRequest('participantsupdate', $payloads, 'POST');
    }

    public function updateGroupSetting($groupId, $action)
    {
        $payloads = [
            'id' => $groupId,
            'action' => $action,
        ];

        return $this->makeRequest('settingsupdate', $payloads, 'POST');
    }

    public function updateGroupSubject($groupId, $subject)
    {
        $payloads = [
            'id' => $groupId,
            'subject' => $subject,
        ];

        return $this->makeRequest('updatesubject', $payloads, 'POST');
    }

    public function updateGroupDescription($groupId, $description)
    {
        $payloads = [
            'id' => $groupId,
            'description' => $description,
        ];

        return $this->makeRequest('updatedescription', $payloads, 'POST');
    }
}
