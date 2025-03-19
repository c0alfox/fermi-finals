const api_root = "http://localhost/TPSI/Progetto/api/v1/";

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
 *   message: string,
 *   user_data: {
 *     Email: string,
 *     Nome: string,
 *     Cognome: string,
 *     DataOraUtente: string,
 *     Bio: string|null,
 *     NumProgetti: number
 *   },
 *   projects: Array<any>
 * }} UserProfile
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
const API = {
    fetch: async (request_method, endpoint, request_body, options) => {
        const resp = await fetch(api_root + endpoint, {
            method: request_method,
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${get_jwt()}`,
                ...(options?.headers ?? {})
            },
            body: JSON.stringify(request_body),
            ...options
        });

        let {jwt, ...json} = await resp.json();
        set_jwt(jwt ?? get_jwt());

        return { status: resp.status, ok: resp.ok, data: json };
    },
    user_profile: (account_id = null) => {
        if (account_id) {
            return api_fetch('GET', `account.php?user_id=${account_id}`);
        }

        return api_fetch('GET', 'account.php');
    }
};

export default API;