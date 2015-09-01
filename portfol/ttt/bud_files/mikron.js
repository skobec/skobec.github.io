/*
Mikron 0.0.1
@author: sciner <sciner@ya.ru>
@since 2014-04-30
*/

var Mikron = {

    list_id_list: {},
    list_events: {
        before_load_edit_form: 1,
        before_load_edit_form: 2,
        onload_edit_form: 3,
        before_item_delete: 4
    },

    init: function(){},

    /**
    * Добавление слушателя событий списка
    * @author: sciner
    * @since 2014-05-06
    *
    * @param string list_id
    * @param callback callback
    *
    * @returns {Boolean}
    */
    addListHandler: function(list_id, callback) {
        if(!(list_id in this.list_id_list)) {
            this.list_id_list[list_id] = new Array();
        }
        // .unshift() aka Prepend: http://stackoverflow.com/questions/8073673/how-can-i-add-new-array-elements-at-the-top-of-an-array-in-javascript
        this.list_id_list[list_id].unshift(callback);
        // console.log(this.list_id_list);
        return true;
    },

    /**
    * Called by Mikron template automatically
    * @author sciner
    * 
    * @param string list_id
    * @param string event
    * @param Array params
    * 
    * @returns {Boolean}
    */
    raiseListHandlers: function(list_id, event, params) {
        if(!(list_id in this.list_id_list)) {
            return false;
        }
        var c = this.list_id_list[list_id]
        for (key in c) {
            if(c[key] instanceof Function) {
                if(c[key](list_id, event, params)) {
                    return true;
                    break;
                }
            }
        }
        return false;
    }

}

$(function($) {
    Mikron.init();
});