<?php

namespace Del\Exception;

use Exception;

class UserException extends Exception
{
    const UNAUTHORISED = 'You are not allowed to perform this action.';
    const USER_ACTIVATED = 'This user is already activated.';
    const USER_EXISTS = 'This user already exists.';
    const USER_NOT_FOUND = 'No user account was found.';
    const USER_UNACTIVATED = 'This account has not been activated.';
    const USER_DISABLED = 'This user account has been closed.';
    const USER_BANNED = 'This user account is currently suspended.';
    const WRONG_PASSWORD = 'The password didn\'t match.';
    const PERSON_EXISTS = 'An existing person on the system has been detected';
}
