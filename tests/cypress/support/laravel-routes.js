Cypress.Laravel = {
  routes: {},

  route: (name, parameters = {}) => {
    assert(
      Object.prototype.hasOwnProperty.call(Cypress.Laravel.routes, name),
      `Laravel route "${name}" does not exist.`,
    );

    return ((uri) => {
      Object.keys(parameters).forEach((parameter) => {
        uri = uri.replace(new RegExp(`{${parameter}}`), parameters[parameter]);
      });

      return uri;
    })(Cypress.Laravel.routes[name].uri);
  },
};
