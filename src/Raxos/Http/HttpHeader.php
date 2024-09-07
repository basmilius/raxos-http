<?php
declare(strict_types=1);

namespace Raxos\Http;

/**
 * Enum HttpHeader
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http
 * @since 1.1.0
 */
enum HttpHeader: string
{

    #region Permanent Message Headers

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case A_IM = 'a-im';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-5.3.2
     */
    case ACCEPT = 'accept';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case ACCEPT_ADDITIONS = 'accept-additions';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-5.3.3
     */
    case ACCEPT_CHARSET = 'accept-charset';

    /**
     * @see https://tools.ietf.org/html/rfc7089
     */
    case ACCEPT_DATETIME = 'accept-datetime';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-5.3.4
     * @see https://tools.ietf.org/html/rfc7694#section-3
     */
    case ACCEPT_ENCODING = 'accept-encoding';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case ACCEPT_FEATURES = 'accept-features';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-5.3.5
     */
    case ACCEPT_LANGUAGE = 'accept-language';

    /**
     * @see https://tools.ietf.org/html/rfc5789
     */
    case ACCEPT_PATCH = 'accept-patch';

    /**
     * @see https://www.w3.org/TR/ldp/
     */
    case ACCEPT_POST = 'accept-post';

    /**
     * @see https://tools.ietf.org/html/rfc7233#section-2.3
     */
    case ACCEPT_RANGES = 'accept-ranges';

    /**
     * @see https://tools.ietf.org/html/rfc7234#section-5.1
     */
    case AGE = 'age';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-7.4.1
     */
    case ALLOW = 'allow';

    /**
     * @see https://tools.ietf.org/html/rfc7639#section-2
     */
    case ALPN = 'alpn';

    /**
     * @see https://tools.ietf.org/html/rfc7838
     */
    case ALT_SVC = 'alt-svc';

    /**
     * @see https://tools.ietf.org/html/rfc7838
     */
    case ALT_USED = 'alt-used';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case ALTERNATES = 'alternates';

    /**
     * @see https://tools.ietf.org/html/rfc4437
     */
    case APPLY_TO_REDIRECT_REF = 'apply-to-redirect-ref';

    /**
     * @see https://tools.ietf.org/html/rfc8053#section-4
     */
    case AUTHENTICATION_CONTROL = 'authentication-control';

    /**
     * @see https://tools.ietf.org/html/rfc7615#section-3
     */
    case AUTHENTICATION_INFO = 'authentication-info';

    /**
     * @see https://tools.ietf.org/html/rfc7235#section-4.2
     */
    case AUTHORIZATION = 'authorization';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case C_EXT = 'c-ext';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case C_MAN = 'c-man';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case C_OPT = 'c-opt';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case C_PEP = 'c-pep';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case C_PEP_INFO = 'c-pep-info';

    /**
     * @see https://tools.ietf.org/html/rfc7234#section-5.2
     */
    case CACHE_CONTROL = 'cache-control';

    /**
     * @see https://tools.ietf.org/html/rfc7809#section-7.1
     */
    case CALDAV_TIMEZONES = 'caldav-timezones';

    /**
     * @see https://tools.ietf.org/html/rfc7230#section-8.1
     */
    case CLOSE = 'close';

    /**
     * @see https://tools.ietf.org/html/rfc7230#section-6.1
     */
    case CONNECTION = 'connection';

    /**
     * @deprecated
     * @see https://tools.ietf.org/html/rfc2068
     */
    case CONTENT_BASE = 'content-base';

    /**
     * @see https://tools.ietf.org/html/rfc6266
     */
    case CONTENT_DISPOSITION = 'content-disposition';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-3.1.2.2
     */
    case CONTENT_ENCODING = 'content-encoding';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case CONTENT_ID = 'content-id';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-3.1.3.2
     */
    case CONTENT_LANGUAGE = 'content-language';

    /**
     * @see https://tools.ietf.org/html/rfc7230#section-3.3.2
     */
    case CONTENT_LENGTH = 'content-length';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-3.1.4.2
     */
    case CONTENT_LOCATION = 'content-location';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case CONTENT_MD5 = 'content-md5';

    /**
     * @see https://tools.ietf.org/html/rfc7233#section-4.2
     */
    case CONTENT_RANGE = 'content-range';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case CONTENT_SCRIPT_TYPE = 'content-script-type';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case CONTENT_STYLE_TYPE = 'content-style-type';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-3.1.1.5
     */
    case CONTENT_TYPE = 'content-type';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case CONTENT_VERSION = 'content-version';

    /**
     * @see https://tools.ietf.org/html/rfc6265
     */
    case COOKIE = 'cookie';

    /**
     * @deprecated
     * @see https://tools.ietf.org/html/rfc2965
     */
    case COOKIE2 = 'cookie2';

    /**
     * @see https://tools.ietf.org/html/rfc5323
     */
    case DASL = 'dasl';

    /**
     * @see https://tools.ietf.org/html/rfc4918
     */
    case DAV = 'dav';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-7.1.1.2
     */
    case DATE = 'date';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case DEFAULT_STYLE = 'default-style';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case DELTA_BASE = 'delta-base';

    /**
     * @see https://tools.ietf.org/html/rfc4918
     */
    case DEPTH = 'depth';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case DERIVED_FROM = 'derived-from';

    /**
     * @see https://tools.ietf.org/html/rfc4918
     */
    case DESTINATION = 'destination';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case DIFFERENTIAL_ID = 'differential-id';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case DIGEST = 'digest';

    /**
     * @see https://tools.ietf.org/html/rfc7232#section-2.3
     */
    case ETAG = 'etag';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-5.1.1
     */
    case EXPECT = 'expect';

    /**
     * @see https://tools.ietf.org/html/rfc7234#section-5.3
     */
    case EXPIRES = 'expires';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case EXT = 'ext';

    /**
     * @see https://tools.ietf.org/html/rfc7239
     */
    case FORWARDED = 'forwarded';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-5.5.1
     */
    case FROM = 'from';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case GETPROFILE = 'getprofile';

    /**
     * @see https://tools.ietf.org/html/rfc7486#section-6.1.1
     */
    case HOBAREG = 'hobareg';

    /**
     * @see https://tools.ietf.org/html/rfc7230#section-5.4
     */
    case HOST = 'host';

    /**
     * @see https://tools.ietf.org/html/rfc7540#section-3.2.1
     */
    case HTTP2_SETTINGS = 'http2-settings';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case IM = 'im';

    /**
     * @see https://tools.ietf.org/html/rfc4918
     */
    case IF = 'if';

    /**
     * @see https://tools.ietf.org/html/rfc7232#section-3.1
     */
    case IF_MATCH = 'if-match';

    /**
     * @see https://tools.ietf.org/html/rfc7232#section-3.3
     */
    case IF_MODIFIED_SINCE = 'if-modified-since';

    /**
     * @see https://tools.ietf.org/html/rfc7232#section-3.2
     */
    case IF_NONE_MATCH = 'if-none-match';

    /**
     * @see https://tools.ietf.org/html/rfc7233#section-3.2
     */
    case IF_RANGE = 'if-range';

    /**
     * @see https://tools.ietf.org/html/rfc6638
     */
    case IF_SCHEDULE_TAG_MATCH = 'if-schedule-tag-match';

    /**
     * @see https://tools.ietf.org/html/rfc7232#section-3.4
     */
    case IF_UNMODIFIED_SINCE = 'if-unmodified-since';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case KEEP_ALIVE = 'keep-alive';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case LABEL = 'label';

    /**
     * @see https://tools.ietf.org/html/rfc7232#section-2.2
     */
    case LAST_MODIFIED = 'last-modified';

    /**
     * @see https://tools.ietf.org/html/rfc5988
     */
    case LINK = 'link';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-7.1.2
     */
    case LOCATION = 'location';

    /**
     * @see https://tools.ietf.org/html/rfc4918
     */
    case LOCK_TOKEN = 'lock-token';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case MAN = 'man';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-5.1.2
     */
    case MAX_FORWARDS = 'max-forwards';

    /**
     * @see https://tools.ietf.org/html/rfc7089
     */
    case MEMENTO_DATETIME = 'memento-datetime';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case METER = 'meter';

    /**
     * @see https://tools.ietf.org/html/rfc7231
     */
    case MIME_VERSION = 'mime-version';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case NEGOTIATE = 'negotiate';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case OPT = 'opt';

    /**
     * @see https://tools.ietf.org/html/rfc8053#section-3
     */
    case OPTIONAL_WWW_AUTHENTICATE = 'optional-www-authenticate';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case ORDERING_TYPE = 'ordering-type';

    /**
     * @see https://tools.ietf.org/html/rfc6454
     */
    case ORIGIN = 'origin';

    /**
     * @see https://tools.ietf.org/html/rfc4918
     */
    case OVERWRITE = 'overwrite';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case P3P = 'p3p';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case PEP = 'pep';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case PICS_LABEL = 'pics-label';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case PEP_INFO = 'pep-info';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case POSITION = 'position';

    /**
     * @see https://tools.ietf.org/html/rfc7234#section-5.4
     */
    case PRAGMA = 'pragma';

    /**
     * @see https://tools.ietf.org/html/rfc7240
     */
    case PREFER = 'prefer';

    /**
     * @see https://tools.ietf.org/html/rfc7240
     */
    case PREFERENCE_APPLIED = 'preference-applied';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case PROFILEOBJECT = 'profileobject';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case PROTOCOL = 'protocol';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case PROTOCOL_INFO = 'protocol-info';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case PROTOCOL_QUERY = 'protocol-query';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case PROTOCOL_REQUEST = 'protocol-request';

    /**
     * @see https://tools.ietf.org/html/rfc7235#section-4.3
     */
    case PROXY_AUTHENTICATE = 'proxy-authenticate';

    /**
     * @see https://tools.ietf.org/html/rfc7615#section-4
     */
    case PROXY_AUTHENTICATION_INFO = 'proxy-authentication-info';

    /**
     * @see https://tools.ietf.org/html/rfc7235#section-4.4
     */
    case PROXY_AUTHORIZATION = 'proxy-authorization';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case PROXY_FEATURES = 'proxy-features';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case PROXY_INSTRUCTION = 'proxy-instruction';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case PUBLIC = 'public';

    /**
     * @see https://tools.ietf.org/html/rfc7469
     */
    case PUBLIC_KEY_PINS = 'public-key-pins';

    /**
     * @see https://tools.ietf.org/html/rfc7469
     */
    case PUBLIC_KEY_PINS_REPORT_ONLY = 'public-key-pins-report-only';

    /**
     * @see https://tools.ietf.org/html/rfc7233#section-3.1
     */
    case RANGE = 'range';

    /**
     * @see https://tools.ietf.org/html/rfc4437
     */
    case REDIRECT_REF = 'redirect-ref';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-5.5.2
     */
    case REFERER = 'referer';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-7.1.3
     */
    case RETRY_AFTER = 'retry-after';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case SAFE = 'safe';

    /**
     * @see https://tools.ietf.org/html/rfc6638
     */
    case SCHEDULE_REPLY = 'schedule-reply';

    /**
     * @see https://tools.ietf.org/html/rfc6638
     */
    case SCHEDULE_TAG = 'schedule-tag';

    /**
     * @see https://tools.ietf.org/html/rfc6455
     */
    case SEC_WEBSOCKET_ACCEPT = 'sec-websocket-accept';

    /**
     * @see https://tools.ietf.org/html/rfc6455
     */
    case SEC_WEBSOCKET_EXTENSIONS = 'sec-websocket-extensions';

    /**
     * @see https://tools.ietf.org/html/rfc6455
     */
    case SEC_WEBSOCKET_KEY = 'sec-websocket-key';

    /**
     * @see https://tools.ietf.org/html/rfc6455
     */
    case SEC_WEBSOCKET_PROTOCOL = 'sec-websocket-protocol';

    /**
     * @see https://tools.ietf.org/html/rfc6455
     */
    case SEC_WEBSOCKET_VERSION = 'sec-websocket-version';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case SECURITY_SCHEME = 'security-scheme';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-7.4.2
     */
    case SERVER = 'server';

    /**
     * @see https://tools.ietf.org/html/rfc6265
     */
    case SET_COOKIE = 'set-cookie';

    /**
     * @deprecated
     * @see https://tools.ietf.org/html/rfc2965
     */
    case SET_COOKIE2 = 'set-cookie2';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case SETPROFILE = 'setprofile';

    /**
     * @see https://tools.ietf.org/html/rfc5023
     */
    case SLUG = 'slug';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case SOAPACTION = 'soapaction';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case STATUS_URI = 'status-uri';

    /**
     * @see https://tools.ietf.org/html/rfc6797
     */
    case STRICT_TRANSPORT_SECURITY = 'strict-transport-security';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case SURROGATE_CAPABILITY = 'surrogate-capability';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case SURROGATE_CONTROL = 'surrogate-control';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case TCN = 'tcn';

    /**
     * @see https://tools.ietf.org/html/rfc7230#section-4.3
     */
    case TE = 'te';

    /**
     * @see https://tools.ietf.org/html/rfc4918
     */
    case TIMEOUT = 'timeout';

    /**
     * @see https://tools.ietf.org/html/rfc8030#section-5.4
     */
    case TOPIC = 'topic';

    /**
     * @see https://tools.ietf.org/html/rfc7230#section-4.4
     */
    case TRAILER = 'trailer';

    /**
     * @see https://tools.ietf.org/html/rfc7230#section-3.3.1
     */
    case TRANSFER_ENCODING = 'transfer-encoding';

    /**
     * @see https://tools.ietf.org/html/rfc8030#section-5.2
     */
    case TTL = 'ttl';

    /**
     * @see https://tools.ietf.org/html/rfc8030#section-5.3
     */
    case URGENCY = 'urgency';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case URI = 'uri';

    /**
     * @see https://tools.ietf.org/html/rfc7230#section-6.7
     */
    case UPGRADE = 'upgrade';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-5.5.3
     */
    case USER_AGENT = 'user-agent';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case VARIANT_VARY = 'variant-vary';

    /**
     * @see https://tools.ietf.org/html/rfc7231#section-7.1.4
     */
    case VARY = 'vary';

    /**
     * @see https://tools.ietf.org/html/rfc7230#section-5.7.1
     */
    case VIA = 'via';

    /**
     * @see https://tools.ietf.org/html/rfc7235#section-4.1
     */
    case WWW_AUTHENTICATE = 'www-authenticate';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case WANT_DIGEST = 'want-digest';

    /**
     * @see https://tools.ietf.org/html/rfc7234#section-5.5
     */
    case WARNING = 'warning';

    /**
     * @see https://tools.ietf.org/html/rfc7034
     */
    case X_FRAME_OPTIONS = 'x-frame-options';

    #endregion

    #region Provisional Message Headers

    /**
     * @deprecated
     */
    case ACCESS_CONTROL = 'access-control';

    /**
     * @see https://fetch.spec.whatwg.org/#http-requests
     */
    case ACCESS_CONTROL_ALLOW_CREDENTIALS = 'access-control-allow-credentials';

    case ACCESS_CONTROL_ALLOW_HEADERS = 'access-control-allow-headers';

    case ACCESS_CONTROL_ALLOW_METHODS = 'access-control-allow-methods';

    case ACCESS_CONTROL_ALLOW_ORIGIN = 'access-control-allow-origin';

    case ACCESS_CONTROL_EXPOSE_HEADERS = 'access-control-expose-headers';

    case ACCESS_CONTROL_MAX_AGE = 'access-control-max-age';

    case ACCESS_CONTROL_REQUEST_METHOD = 'access-control-request-method';

    case ACCESS_CONTROL_REQUEST_HEADERS = 'access-control-request-headers';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case COMPLIANCE = 'compliance';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case CONTENT_TRANSFER_ENCODING = 'content-transfer-encoding';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case COST = 'cost';

    /**
     * @see https://tools.ietf.org/html/rfc6017
     */
    case EDIINT_FEATURES = 'ediint-features';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case MESSAGE_ID = 'message-id';

    /**
     * @deprecated
     */
    case METHOD_CHECK = 'method-check';

    /**
     * @deprecated
     */
    case METHOD_CHECK_EXPIRES = 'method-check-expires';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case NON_COMPLIANCE = 'non-compliance';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case OPTIONAL = 'optional';

    /**
     * @deprecated
     */
    case REFERER_ROOT = 'referer-root';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case RESOLUTION_HINT = 'resolution-hint';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case RESOLVER_LOCATION = 'resolver-location';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case SUBOK = 'subok';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case SUBST = 'subst';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case TITLE = 'title';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case UA_COLOR = 'ua-color';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case UA_MEDIA = 'ua-media';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case UA_PIXELS = 'ua-pixels';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case UA_RESOLUTION = 'ua-resolution';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case UA_WINDOWPIXELS = 'ua-windowpixels';

    /**
     * @see https://tools.ietf.org/html/rfc4229
     */
    case VERSION = 'version';

    case X_DEVICE_ACCEPT = 'x-device-accept';

    case X_DEVICE_ACCEPT_CHARSET = 'x-device-accept-charset';

    case X_DEVICE_ACCEPT_ENCODING = 'x-device-accept-encoding';

    case X_DEVICE_ACCEPT_LANGUAGE = 'x-device-accept-language';

    case X_DEVICE_USER_AGENT = 'x-device-user-agent';

    #endregion

    #region Non-Standard Headers

    /**
     * @see https://www.w3.org/TR/CSP3/#csp-header
     */
    case CONTENT_SECURITY_POLICY = 'content-security-policy';

    case DNT = 'dnt';

    case PROXY_CONNECTION = 'proxy-connection';

    case STATUS = 'status';

    case UPGRADE_INSECURE_REQUESTS = 'upgrade-insecure-requests';

    case X_CONTENT_DURATION = 'x-content-duration';

    case X_CONTENT_SECURITY_POLICY = 'x-content-security-policy';

    case X_CONTENT_TYPE_OPTIONS = 'x-content-type-options';

    case X_CORRELATION_ID = 'x-correlation-id';

    case X_CSRF_TOKEN = 'x-csrf-token';

    case X_FORWARDED_FOR = 'x-forwarded-for';

    case X_FORWARDED_HOST = 'x-forwarded-host';

    case X_FORWARDED_PROTO = 'x-forwarded-proto';

    case X_HTTP_METHOD_OVERRIDE = 'x-http-method-override';

    case X_POWERED_BY = 'x-powered-by';

    case X_REQUEST_ID = 'x-request-id';

    case X_REQUESTED_WITH = 'x-requested-with';

    case X_UA_COMPATIBLE = 'x-ua-compatible';

    case X_UIDH = 'x-uidh';

    case X_WAP_PROFILE = 'x-wap-profile';

    case X_WEBKIT_CSP = 'x-webkit-csp';

    case X_XSS_PROTECTION = 'x-xss-protection';

    #endregion

}
