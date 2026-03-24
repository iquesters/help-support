<?php

namespace Iquesters\HelpSupport\Constants;

class Constants
{
    public const GITHUB_ACCEPT_HEADER = 'application/vnd.github.v3+json';
    public const GITHUB_RAW_HOST = 'raw.githubusercontent.com';

    public const CACHE_KEY_DOCS_PREFIX = 'github_docs_';
    public const CACHE_KEY_FILE_PREFIX = 'github_file_';

    public const VISIBILITY_SCOPE_ALL = 'all';
    public const VISIBILITY_SCOPE_DEFAULT = 'restricted';

    public const RESPONSE_TEXT_PLAIN = 'text/plain';

    public const ERROR_NO_URL_PROVIDED = 'No URL provided';
    public const ERROR_UNAUTHORIZED_DOCUMENT_ACCESS = 'Unauthorized document access';
    public const ERROR_DOCUMENT_NOT_FOUND = 'Document not found';
    public const ERROR_VIEW_NOT_FOUND = 'Help support view not found.';
}
