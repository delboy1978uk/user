<?php

namespace Del\Exception;


class EmailLinkException
{
    const LINK_NOT_FOUND = 'A matching email link was not found';
    const LINK_EXPIRED = 'A matching email link was not found';
    const LINK_NO_MATCH = 'The token did not match.';
}