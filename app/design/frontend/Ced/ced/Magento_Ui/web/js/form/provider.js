/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'underscore',
    'uiElement',
    './client',
    'mageUtils'
], function (_, Element, Client, utils) {
    'use strict';

    return Element.extend({
        defaults: {
            clientConfig: {
                urls: {
                	
                    save: '${ $.submit_url }',
                    beforeSave: '${ $.validate_url }'
                }
            }
        },

        /**
         * Initializes provider component.
         *
         * @returns {Provider} Chainable.
         */
        initialize: function () {
            this._super()
                .initClient();

            return this;
        },

        /**
         * Initializes client component.
         *
         * @returns {Provider} Chainable.
         */
        initClient: function () {
            this.client = new Client(this.clientConfig);

            return this;
        },

        /**
         * Saves currently available data.
         *
         * @param {Object} [options] - Addtitional request options.
         * @returns {Provider} Chainable.
         */
        save: function (options) {
            var data = this.get('data');

            this.client.save(data, options);

            return this;
        },

        /**
         * Update data that stored in provider.
         *
         * @param {Boolean} isProvider
         * @param {Object} newData
         * @param {Object} oldData
         *
         * @returns {Provider}
         */
        updateConfig: function (isProvider, newData, oldData) {
            if (isProvider === true) {
                this.setData(oldData, newData, this);
            }

            return this;
        },

        /**
         *  Set data to provder based on current data.
         *
         * @param {Object} oldData
         * @param {Object} newData
         * @param {Provider} current
         * @param {String} parentPath
         */
        setData: function (oldData, newData, current, parentPath) {

            /* eslint-disable eqeqeq */
            _.each(newData, function (val, key) {
                if (_.isObject(val) || _.isArray(val)) {
                    this.setData(oldData[key], val, current[key], utils.fullPath(parentPath, key));
                } else if (val != oldData[key] && oldData[key] == current[key]) {
                    this.set(utils.fullPath(parentPath, key), val);
                }
            }, this);

            /* eslint-enable eqeqeq */
        }
    });
});
