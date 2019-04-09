/**
 *
 * @param url
 * @param access_token
 * @param chat_id
 * @constructor
 *
 * Methods - Методы
 * init - Нужно вызвать для применение настроек.
 * sendMessage - Отправить сообщение
 *
 *
 * Events - Событие
 * onOpen - Когда открывается сооедение
 * onMessage - Когда приходять сообщение
 * onClose - Когда закрывается сооеденение
 * onError - Когда есть ошибка
 *
 *
 * diolog_1: "1"
 diolog_2: "2"
 diolog_type: "personal"
 from_user_id: 24
 message: "sadsd"
 to_user_id: ""
 _from: {}
 _message:
 created_at: 1554787230
 deleted_at: 0
 from_status: 1
 from_user_id: 24
 isDeleted: 0
 is_seen: 0
 message: "sadsd"
 message_id: 44
 replay_message_id: null
 to_chat_id: 25
 to_status: 1
 to_user_id: null
 type: 1
 updated_at: 1554787230
 __proto__: Object
 _request: "{"type":"message","data":"sadsd"}"
 */
var ChatClient = function (url, access_token, diolog_1, diolog_2, diolog_type, to_user_id) {
    var self = this;
    this.access_token = access_token;
    this.url = url;

    this.diolog_1 = diolog_1;
    this.diolog_2 = diolog_2;
    this.diolog_type = diolog_type;
    this.to_user_id = to_user_id;


    /**
     *
     * @type {WebSocket}
     */
    this.socket = new WebSocket(
        self.url +
        '?access_token=' + self.access_token +
        '&diolog_1=' + self.diolog_1 +
        '&diolog_2=' + self.diolog_2 +
        '&diolog_type=' + self.diolog_type +
        '&to_user_id=' + self.to_user_id
    );

    /**
     * Нужно вызвать для применение настроек.
     */
    this.init = function () {
        self.socket.onmessage = function (event) {
            var msg = JSON.parse(event.data);
            var type = msg.type;
            var data = msg.data;
            self.onMessage(type, data, event);
        };

        self.socket.onerror = function (event) {
            self.onError(event);
        };

        self.socket.onclose = function (event) {
            self.onClose(event);
        };

        self.socket.onopen = function (event) {
            self.onOpen(event);
        };

    }

    /**
     *
     * @param text
     */
    this.sendMessage = function (text) {
        self.socket.send(JSON.stringify({
            type: "message",
            data: text
        }));
        console.log("send");
    }

    /**
     *
     * @param type
     * @param data
     * @param event
     */
    this.onMessage = function (type, data, event) {
        console.log(type, data);
    }

    /**
     *
     * @param event
     */
    this.onError = function (event) {
        console.log(event);
    }

    /**
     *
     * @param event
     */
    this.onClose = function (event) {
        console.log(event);
    }

    /**
     *
     * @param event
     */
    this.onOpen = function (event) {
        console.log(event);
    }

}