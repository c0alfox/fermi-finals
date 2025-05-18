const api_root = "/api/";

/**
 * @template T
 * @typedef {{
 *  status: string,
 *  ok: true,
 *  data: T
 * } | {
 *  status: string,
 *  ok: false,
 *  data: ErrorData
 * }} APIResponse
 */

/**
 * @typedef {{
 *  message: string
 * }} ErrorData
 */

/**
 * @type {{
 *   fetch: (request_method: string, endpoint: string, request_body: Object, options: Object) => Promise<APIResponse>,
 *   user_profile: (account_id: number?) => Promise<APIResponse<UserProfile>>
 * }}
 */

export default async function api_fetch(request_method, endpoint, request_body, options={}) {
    const resp = await fetch(api_root + endpoint, {
        method: request_method,
        headers: {
            'Content-Type': 'application/json',
            ...(options?.headers ?? {})
        },
        credentials: 'include',
        body: JSON.stringify(request_body),
        ...options
    });

    let {jwt, ...json} = await resp.clone().json()
        .then(data => data)
        .catch(async _ => { throw new Error(await resp.text()) });

    return { status: resp.status, ok: resp.ok, data: json };
}