var MyApp = (function () {
    var _data = {}; // Almacenar datos temporales
    var _eventHandlers = {}; // Almacenar eventos

    return {
        /** Manejo de datos */
        data: {
            get: function (key) {
                return _data[key] || null;
            },
            set: function (key, value) {
                _data[key] = value;
            },
            remove: function (key) {
                delete _data[key];
            }
        },

        /** Manejo del formulario */
        form: {
            getFieldValue: function (fieldId) {
                var field = document.getElementById(fieldId);
                if (!field) return null;
                
                if (field.tagName === "SELECT") {
                    return field.options[field.selectedIndex].value;
                }
                return field.value;
            },
            setFieldValue: function (fieldId, value) {
                var field = document.getElementById(fieldId);
                if (!field) return;
                var wasDisabled = field.disabled;
                if (wasDisabled) field.disabled = false;

                if (field.tagName === "SELECT") {
                    field.value = value;
                } else {
                    field.value = value;
                }
                field.offsetHeight;
                if (wasDisabled) field.disabled = true;
				field.dispatchEvent( new Event("change", { bubbles: true }));
            },
            disableField: function (fieldId, disabled) {
                var field = document.getElementById(fieldId);
                if (field) field.disabled = disabled;
            },
            addEventListener: function (fieldId, event, callback) {
                var field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener(event, callback);
                    _eventHandlers[fieldId] = _eventHandlers[fieldId] || {};
                    _eventHandlers[fieldId][event] = callback;
                }
            },
            removeEventListener: function (fieldId, event) {
                var field = document.getElementById(fieldId);
                if (field && _eventHandlers[fieldId] && _eventHandlers[fieldId][event]) {
                    field.removeEventListener(event, _eventHandlers[fieldId][event]);
                    delete _eventHandlers[fieldId][event];
                }
            },
            clearImage: function (fieldId) {
                debugger;
                var field = document.getElementById(fieldId + '_image');
                field.removeAttribute("src");
                field.parentElement.style.display = "none";
                var id = MyApp.form.getFieldValue('id');
                var entityname = MyApp.form.getEntityName();
                var record = { };
                record['id'] = id;
                record[fieldId] = '';
                MyApp.api.upsert(entityname, [record]);
            },
            getEntityName: function (){
                const parametros = new URLSearchParams(window.location.search);
                return parametros.get("controller"); 
            }


        },

        /** Manejo de entidades */
        entity: {
            _fields: {},
            get: function (field) {
                return this._fields[field] || null;
            },
            set: function (field, value) {
                this._fields[field] = value;
            },
            remove: function (field) {
                delete this._fields[field];
            }
        },

        /** Comunicación con API */
        api: {
            query: function (json_query) {
                const encodedjson = encodeURIComponent(json_query);
                const url = `/api/entity.php?query=${encodedjson}`;

                const config = {
                    method:  "GET",
                    headers: {"Content-Type": "application/json" },
                };

                return new Promise((resolve, reject) => {
                    fetch(url, config)
                    .then(response => {
                        if (!response.ok) {
                            // Rechaza si hay error HTTP (404, 500, etc.)
                            return response.json().then(err => reject({
                                status: response.status,
                                message: response.statusText,
                                body: err
                            }));
                        }
                        return response.json(); // Si todo va bien
                    })
                    .then(data => resolve(data))
                    .catch(error => reject({
                        status: null,
                        message: "Network or parsing error",
                        body: error
                    }));
                });

            },
            upsert: function (entity, object_list) {
                
                const url = `/api/entity.php?controller=${entity}`;

                const config = {
                    method:  "POST",
                    headers: {"Content-Type": "application/json" },
                    body: JSON.stringify(object_list)
                };

                return new Promise((resolve, reject) => {
                    fetch(url, config)
                    .then(response => {
                        if (!response.ok) {
                            // Rechaza si hay error HTTP (404, 500, etc.)
                            return response.json().then(err => reject({
                                status: response.status,
                                message: response.statusText,
                                body: err
                            }));
                        }
                        return response.json(); // Si todo va bien
                    })
                    .then(data => resolve(data))
                    .catch(error => reject({
                        status: null,
                        message: "Network or parsing error",
                        body: error
                    }));
                });

            },
            retrieve : function (entity, id) {
                
                const url = `/api/entity.php?controller=${entity}&item=${id}`;

                const config = {
                    method:  "GET",
                    headers: {"Content-Type": "application/json" }
                };

                return new Promise((resolve, reject) => {
                    fetch(url, config)
                    .then(response => {
                        if (!response.ok) {
                            // Rechaza si hay error HTTP (404, 500, etc.)
                            return response.json().then(err => reject({
                                status: response.status,
                                message: response.statusText,
                                body: err
                            }));
                        }
                        return response.json(); // Si todo va bien
                    })
                    .then(data => resolve(data))
                    .catch(error => reject({
                        status: null,
                        message: "Network or parsing error",
                        body: error
                    }));
                });

            }
        },

        /** UI y Notificaciones */
        ui: {
            notify: function (message, type = "info") {
                var notification = document.createElement("div");
                notification.className = "message";
                notification.innerText = message;
				var main_section = document.getElementById('main');
				main_section.insertBefore(notification, main_section.firstChild);
                //document.body.appendChild(notification);
                //setTimeout(() => notification.remove(), 10000);
            }
        },

        /** Utilidades generales */
        utils: {
            log: function (message) {
                console.log("[MyApp] " + message);
            },
			loadScript(url, callback){
				var script = document.createElement("script");
				script.src = url;
				script.onload = callback; // Ejecuta la función cuando cargue
				document.head.appendChild(script);
			}
        }
    };
})();
