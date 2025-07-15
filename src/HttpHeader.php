<?php
declare(strict_types=1);

namespace Raxos\Http;

/**
 * Class HttpHeader
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http
 * @since 1.1.0
 */
final class HttpHeader
{

    #region Permanent Message Headers

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string A_IM = 'a-im';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-5.3.2
     */
    public const string ACCEPT = 'accept';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string ACCEPT_ADDITIONS = 'accept-additions';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-5.3.3
     */
    public const string ACCEPT_CHARSET = 'accept-charset';

    /**
     * @see https://tools.ietf.org/html/rfc7089
     */
    public const string ACCEPT_DATETIME = 'accept-datetime';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-5.3.4
     * @see https://tools.ietf.org/html/rfc7694#section-3
     */
    public const string ACCEPT_ENCODING = 'accept-encoding';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string ACCEPT_FEATURES = 'accept-features';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-5.3.5
     */
    public const string ACCEPT_LANGUAGE = 'accept-language';

    /**
     * @see https://tools.ietf.org/html/rfc5789
     */
    public const string ACCEPT_PATCH = 'accept-patch';

    /**
     * @see https://www.w3.org/TR/ldp/
     */
    public const string ACCEPT_POST = 'accept-post';

    /**
     * @see https://tools.ietf.org/html/rfc7233#section-2.3
     */
    public const string ACCEPT_RANGES = 'accept-ranges';

    /**
     * @see https://tools.ietf.org/html/rfc7234#section-5.1
     */
    public const string AGE = 'age';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-7.4.1
     */
    public const string ALLOW = 'allow';

    /**
     * @see https://tools.ietf.org/html/rfc7639#section-2
     */
    public const string ALPN = 'alpn';

    /**
     * @see https://tools.ietf.org/html/rfc7838
     */
    public const string ALT_SVC = 'alt-svc';

    /**
     * @see https://tools.ietf.org/html/rfc7838
     */
    public const string ALT_USED = 'alt-used';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string ALTERNATES = 'alternates';

    /**
     * @see https://tools.ietf.org/html/rfc4437
     */
    public const string APPLY_TO_REDIRECT_REF = 'apply-to-redirect-ref';

    /**
     * @see https://tools.ietf.org/html/rfc8053#section-4
     */
    public const string AUTHENTICATION_CONTROL = 'authentication-control';

    /**
     * @see https://tools.ietf.org/html/rfc7615#section-3
     */
    public const string AUTHENTICATION_INFO = 'authentication-info';

    /**
     * @see https://tools.ietf.org/html/rfc7235#section-4.2
     */
    public const string AUTHORIZATION = 'authorization';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string C_EXT = 'c-ext';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string C_MAN = 'c-man';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string C_OPT = 'c-opt';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string C_PEP = 'c-pep';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string C_PEP_INFO = 'c-pep-info';

    /**
     * @see https://tools.ietf.org/html/rfc7234#section-5.2
     */
    public const string CACHE_CONTROL = 'cache-control';

    /**
     * @see https://tools.ietf.org/html/rfc7809#section-7.1
     */
    public const string CALDAV_TIMEZONES = 'caldav-timezones';

    /**
     * @see https://tools.ietf.org/html/rfc7230#section-8.1
     */
    public const string CLOSE = 'close';

    /**
     * @see https://tools.ietf.org/html/rfc7230#section-6.1
     */
    public const string CONNECTION = 'connection';

    /**
     * @deprecated
     * @see https://tools.ietf.org/html/rfc2068
     */
    public const string CONTENT_BASE = 'content-base';

    /**
     * @see https://tools.ietf.org/html/rfc6266
     */
    public const string CONTENT_DISPOSITION = 'content-disposition';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-3.1.2.2
     */
    public const string CONTENT_ENCODING = 'content-encoding';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string CONTENT_ID = 'content-id';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-3.1.3.2
     */
    public const string CONTENT_LANGUAGE = 'content-language';

    /**
     * @see https://tools.ietf.org/html/rfc7230#section-3.3.2
     */
    public const string CONTENT_LENGTH = 'content-length';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-3.1.4.2
     */
    public const string CONTENT_LOCATION = 'content-location';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string CONTENT_MD5 = 'content-md5';

    /**
     * @see https://tools.ietf.org/html/rfc7233#section-4.2
     */
    public const string CONTENT_RANGE = 'content-range';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string CONTENT_SCRIPT_TYPE = 'content-script-type';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string CONTENT_STYLE_TYPE = 'content-style-type';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-3.1.1.5
     */
    public const string CONTENT_TYPE = 'content-type';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string CONTENT_VERSION = 'content-version';

    /**
     * @see https://tools.ietf.org/html/rfc6265
     */
    public const string COOKIE = 'cookie';

    /**
     * @deprecated
     * @see https://tools.ietf.org/html/rfc2965
     */
    public const string COOKIE2 = 'cookie2';

    /**
     * @see https://tools.ietf.org/html/rfc5323
     */
    public const string DASL = 'dasl';

    /**
     * @see https://tools.ietf.org/html/rfc4918
     */
    public const string DAV = 'dav';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-7.1.1.2
     */
    public const string DATE = 'date';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string DEFAULT_STYLE = 'default-style';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string DELTA_BASE = 'delta-base';

    /**
     * @see https://tools.ietf.org/html/rfc4918
     */
    public const string DEPTH = 'depth';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string DERIVED_FROM = 'derived-from';

    /**
     * @see https://tools.ietf.org/html/rfc4918
     */
    public const string DESTINATION = 'destination';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string DIFFERENTIAL_ID = 'differential-id';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string DIGEST = 'digest';

    /**
     * @see https://tools.ietf.org/html/rfc7232#section-2.3
     */
    public const string ETAG = 'etag';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-5.1.1
     */
    public const string EXPECT = 'expect';

    /**
     * @see https://tools.ietf.org/html/rfc7234#section-5.3
     */
    public const string EXPIRES = 'expires';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string EXT = 'ext';

    /**
     * @see https://tools.ietf.org/html/rfc7239
     */
    public const string FORWARDED = 'forwarded';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-5.5.1
     */
    public const string FROM = 'from';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string GETPROFILE = 'getprofile';

    /**
     * @see https://tools.ietf.org/html/rfc7486#section-6.1.1
     */
    public const string HOBAREG = 'hobareg';

    /**
     * @see https://tools.ietf.org/html/rfc7230#section-5.4
     */
    public const string HOST = 'host';

    /**
     * @see https://tools.ietf.org/html/rfc7540#section-3.2.1
     */
    public const string HTTP2_SETTINGS = 'http2-settings';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string IM = 'im';

    /**
     * @see https://tools.ietf.org/html/rfc4918
     */
    public const string IF = 'if';

    /**
     * @see https://tools.ietf.org/html/rfc7232#section-3.1
     */
    public const string IF_MATCH = 'if-match';

    /**
     * @see https://tools.ietf.org/html/rfc7232#section-3.3
     */
    public const string IF_MODIFIED_SINCE = 'if-modified-since';

    /**
     * @see https://tools.ietf.org/html/rfc7232#section-3.2
     */
    public const string IF_NONE_MATCH = 'if-none-match';

    /**
     * @see https://tools.ietf.org/html/rfc7233#section-3.2
     */
    public const string IF_RANGE = 'if-range';

    /**
     * @see https://tools.ietf.org/html/rfc6638
     */
    public const string IF_SCHEDULE_TAG_MATCH = 'if-schedule-tag-match';

    /**
     * @see https://tools.ietf.org/html/rfc7232#section-3.4
     */
    public const string IF_UNMODIFIED_SINCE = 'if-unmodified-since';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string KEEP_ALIVE = 'keep-alive';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string LABEL = 'label';

    /**
     * @see https://tools.ietf.org/html/rfc7232#section-2.2
     */
    public const string LAST_MODIFIED = 'last-modified';

    /**
     * @see https://tools.ietf.org/html/rfc5988
     */
    public const string LINK = 'link';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-7.1.2
     */
    public const string LOCATION = 'location';

    /**
     * @see https://tools.ietf.org/html/rfc4918
     */
    public const string LOCK_TOKEN = 'lock-token';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string MAN = 'man';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-5.1.2
     */
    public const string MAX_FORWARDS = 'max-forwards';

    /**
     * @see https://tools.ietf.org/html/rfc7089
     */
    public const string MEMENTO_DATETIME = 'memento-datetime';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string METER = 'meter';

    /**
     * @see https://tools.ietf.org/html/rfc7231
     */
    public const string MIME_VERSION = 'mime-version';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string NEGOTIATE = 'negotiate';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string OPT = 'opt';

    /**
     * @see https://tools.ietf.org/html/rfc8053#section-3
     */
    public const string OPTIONAL_WWW_AUTHENTICATE = 'optional-www-authenticate';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string ORDERING_TYPE = 'ordering-type';

    /**
     * @see https://tools.ietf.org/html/rfc6454
     */
    public const string ORIGIN = 'origin';

    /**
     * @see https://tools.ietf.org/html/rfc4918
     */
    public const string OVERWRITE = 'overwrite';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string P3P = 'p3p';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string PEP = 'pep';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string PICS_LABEL = 'pics-label';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string PEP_INFO = 'pep-info';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string POSITION = 'position';

    /**
     * @see https://tools.ietf.org/html/rfc7234#section-5.4
     */
    public const string PRAGMA = 'pragma';

    /**
     * @see https://tools.ietf.org/html/rfc7240
     */
    public const string PREFER = 'prefer';

    /**
     * @see https://tools.ietf.org/html/rfc7240
     */
    public const string PREFERENCE_APPLIED = 'preference-applied';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string PROFILEOBJECT = 'profileobject';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string PROTOCOL = 'protocol';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string PROTOCOL_INFO = 'protocol-info';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string PROTOCOL_QUERY = 'protocol-query';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string PROTOCOL_REQUEST = 'protocol-request';

    /**
     * @see https://tools.ietf.org/html/rfc7235#section-4.3
     */
    public const string PROXY_AUTHENTICATE = 'proxy-authenticate';

    /**
     * @see https://tools.ietf.org/html/rfc7615#section-4
     */
    public const string PROXY_AUTHENTICATION_INFO = 'proxy-authentication-info';

    /**
     * @see https://tools.ietf.org/html/rfc7235#section-4.4
     */
    public const string PROXY_AUTHORIZATION = 'proxy-authorization';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string PROXY_FEATURES = 'proxy-features';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string PROXY_INSTRUCTION = 'proxy-instruction';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string PUBLIC = 'public';

    /**
     * @see https://tools.ietf.org/html/rfc7469
     */
    public const string PUBLIC_KEY_PINS = 'public-key-pins';

    /**
     * @see https://tools.ietf.org/html/rfc7469
     */
    public const string PUBLIC_KEY_PINS_REPORT_ONLY = 'public-key-pins-report-only';

    /**
     * @see https://tools.ietf.org/html/rfc7233#section-3.1
     */
    public const string RANGE = 'range';

    /**
     * @see https://tools.ietf.org/html/rfc4437
     */
    public const string REDIRECT_REF = 'redirect-ref';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-5.5.2
     */
    public const string REFERER = 'referer';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-7.1.3
     */
    public const string RETRY_AFTER = 'retry-after';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string SAFE = 'safe';

    /**
     * @see https://tools.ietf.org/html/rfc6638
     */
    public const string SCHEDULE_REPLY = 'schedule-reply';

    /**
     * @see https://tools.ietf.org/html/rfc6638
     */
    public const string SCHEDULE_TAG = 'schedule-tag';

    /**
     * @see https://tools.ietf.org/html/rfc6455
     */
    public const string SEC_WEBSOCKET_ACCEPT = 'sec-websocket-accept';

    /**
     * @see https://tools.ietf.org/html/rfc6455
     */
    public const string SEC_WEBSOCKET_EXTENSIONS = 'sec-websocket-extensions';

    /**
     * @see https://tools.ietf.org/html/rfc6455
     */
    public const string SEC_WEBSOCKET_KEY = 'sec-websocket-key';

    /**
     * @see https://tools.ietf.org/html/rfc6455
     */
    public const string SEC_WEBSOCKET_PROTOCOL = 'sec-websocket-protocol';

    /**
     * @see https://tools.ietf.org/html/rfc6455
     */
    public const string SEC_WEBSOCKET_VERSION = 'sec-websocket-version';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string SECURITY_SCHEME = 'security-scheme';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-7.4.2
     */
    public const string SERVER = 'server';

    /**
     * @see https://tools.ietf.org/html/rfc6265
     */
    public const string SET_COOKIE = 'set-cookie';

    /**
     * @deprecated
     * @see https://tools.ietf.org/html/rfc2965
     */
    public const string SET_COOKIE2 = 'set-cookie2';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string SETPROFILE = 'setprofile';

    /**
     * @see https://tools.ietf.org/html/rfc5023
     */
    public const string SLUG = 'slug';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string SOAPACTION = 'soapaction';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string STATUS_URI = 'status-uri';

    /**
     * @see https://tools.ietf.org/html/rfc6797
     */
    public const string STRICT_TRANSPORT_SECURITY = 'strict-transport-security';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string SURROGATE_CAPABILITY = 'surrogate-capability';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string SURROGATE_CONTROL = 'surrogate-control';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string TCN = 'tcn';

    /**
     * @see https://tools.ietf.org/html/rfc7230#section-4.3
     */
    public const string TE = 'te';

    /**
     * @see https://tools.ietf.org/html/rfc4918
     */
    public const string TIMEOUT = 'timeout';

    /**
     * @see https://tools.ietf.org/html/rfc8030#section-5.4
     */
    public const string TOPIC = 'topic';

    /**
     * @see https://tools.ietf.org/html/rfc7230#section-4.4
     */
    public const string TRAILER = 'trailer';

    /**
     * @see https://tools.ietf.org/html/rfc7230#section-3.3.1
     */
    public const string TRANSFER_ENCODING = 'transfer-encoding';

    /**
     * @see https://tools.ietf.org/html/rfc8030#section-5.2
     */
    public const string TTL = 'ttl';

    /**
     * @see https://tools.ietf.org/html/rfc8030#section-5.3
     */
    public const string URGENCY = 'urgency';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string URI = 'uri';

    /**
     * @see https://tools.ietf.org/html/rfc7230#section-6.7
     */
    public const string UPGRADE = 'upgrade';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-5.5.3
     */
    public const string USER_AGENT = 'user-agent';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string VARIANT_VARY = 'variant-vary';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-7.1.4
     */
    public const string VARY = 'vary';

    /**
     * @see https://tools.ietf.org/html/rfc7230#section-5.7.1
     */
    public const string VIA = 'via';

    /**
     * @see https://tools.ietf.org/html/rfc7235#section-4.1
     */
    public const string WWW_AUTHENTICATE = 'www-authenticate';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string WANT_DIGEST = 'want-digest';

    /**
     * @see https://tools.ietf.org/html/rfc7234#section-5.5
     */
    public const string WARNING = 'warning';

    /**
     * @see https://tools.ietf.org/html/rfc7034
     */
    public const string X_FRAME_OPTIONS = 'x-frame-options';

    #endregion

    #region Provisional Message Headers

    /**
     * @deprecated
     */
    public const string ACCESS_CONTROL = 'access-control';

    /**
     * @see https://fetch.spec.whatwg.org/#http-requests
     */
    public const string ACCESS_CONTROL_ALLOW_CREDENTIALS = 'access-control-allow-credentials';

    public const string ACCESS_CONTROL_ALLOW_HEADERS = 'access-control-allow-headers';

    public const string ACCESS_CONTROL_ALLOW_METHODS = 'access-control-allow-methods';

    public const string ACCESS_CONTROL_ALLOW_ORIGIN = 'access-control-allow-origin';

    public const string ACCESS_CONTROL_EXPOSE_HEADERS = 'access-control-expose-headers';

    public const string ACCESS_CONTROL_MAX_AGE = 'access-control-max-age';

    public const string ACCESS_CONTROL_REQUEST_METHOD = 'access-control-request-method';

    public const string ACCESS_CONTROL_REQUEST_HEADERS = 'access-control-request-headers';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string COMPLIANCE = 'compliance';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string CONTENT_TRANSFER_ENCODING = 'content-transfer-encoding';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string COST = 'cost';

    /**
     * @see https://tools.ietf.org/html/rfc6017
     */
    public const string EDIINT_FEATURES = 'ediint-features';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string MESSAGE_ID = 'message-id';

    /**
     * @deprecated
     */
    public const string METHOD_CHECK = 'method-check';

    /**
     * @deprecated
     */
    public const string METHOD_CHECK_EXPIRES = 'method-check-expires';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string NON_COMPLIANCE = 'non-compliance';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string OPTIONAL = 'optional';

    /**
     * @deprecated
     */
    public const string REFERER_ROOT = 'referer-root';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string RESOLUTION_HINT = 'resolution-hint';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string RESOLVER_LOCATION = 'resolver-location';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string SUBOK = 'subok';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string SUBST = 'subst';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string TITLE = 'title';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string UA_COLOR = 'ua-color';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string UA_MEDIA = 'ua-media';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string UA_PIXELS = 'ua-pixels';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string UA_RESOLUTION = 'ua-resolution';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string UA_WINDOWPIXELS = 'ua-windowpixels';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    public const string VERSION = 'version';

    public const string X_DEVICE_ACCEPT = 'x-device-accept';

    public const string X_DEVICE_ACCEPT_CHARSET = 'x-device-accept-charset';

    public const string X_DEVICE_ACCEPT_ENCODING = 'x-device-accept-encoding';

    public const string X_DEVICE_ACCEPT_LANGUAGE = 'x-device-accept-language';

    public const string X_DEVICE_USER_AGENT = 'x-device-user-agent';

    #endregion

    #region Non-Standard Headers

    /**
     * @see https://www.w3.org/TR/CSP3/#csp-header
     */
    public const string CONTENT_SECURITY_POLICY = 'content-security-policy';

    public const string DNT = 'dnt';

    public const string PROXY_CONNECTION = 'proxy-connection';

    public const string STATUS = 'status';

    public const string UPGRADE_INSECURE_REQUESTS = 'upgrade-insecure-requests';

    public const string X_CONTENT_DURATION = 'x-content-duration';

    public const string X_CONTENT_SECURITY_POLICY = 'x-content-security-policy';

    public const string X_CONTENT_TYPE_OPTIONS = 'x-content-type-options';

    public const string X_CORRELATION_ID = 'x-correlation-id';

    public const string X_CSRF_TOKEN = 'x-csrf-token';

    public const string X_FORWARDED_FOR = 'x-forwarded-for';

    public const string X_FORWARDED_HOST = 'x-forwarded-host';

    public const string X_FORWARDED_PROTO = 'x-forwarded-proto';

    public const string X_HTTP_METHOD_OVERRIDE = 'x-http-method-override';

    public const string X_POWERED_BY = 'x-powered-by';

    public const string X_REQUEST_ID = 'x-request-id';

    public const string X_REQUESTED_WITH = 'x-requested-with';

    public const string X_UA_COMPATIBLE = 'x-ua-compatible';

    public const string X_UIDH = 'x-uidh';

    public const string X_WAP_PROFILE = 'x-wap-profile';

    public const string X_WEBKIT_CSP = 'x-webkit-csp';

    public const string X_XSS_PROTECTION = 'x-xss-protection';

    #endregion

}
