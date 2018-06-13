import api from "./api.js";

export default {
  running: 0,
  request(path, options) {
    options = Object.assign(options || {}, {
      credentials: "same-origin",
      headers: {
        "x-requested-with": "xmlhttprequest",
        "content-type": "application/json",
        ...options.headers,
      }
    });

    api.config.onStart();
    this.running++;

    return fetch(api.config.endpoint + "/" + path, options)
      .then(response => response.json())
      .then(json => {
        if (json.status && json.status === "error") {
          throw json;
        }
        this.running--;
        api.config.onComplete();
        api.config.onSuccess(json);
        return json;
      })
      .catch(error => {
        this.running--;
        api.config.onComplete();
        api.config.onError(error);
        throw error;
      });
  },
  get(path, query, options) {
    if (query) {
      path +=
        "?" +
        Object.keys(query)
          .map(key => key + "=" + query[key])
          .join("&");
    }

    return this.request(path, Object.assign(options || {}, { method: "GET" }));
  },
  post(path, data, options, method = "POST") {
    return this.request(
      path,
      Object.assign(options || {}, {
        method: method,
        body: JSON.stringify(data)
      })
    );
  },
  patch(path, data, options) {
    return this.post(path, data, options, "PATCH");
  },
  delete(path) {
    return this.request(path, { method: "DELETE" });
  }
};
