import { defineStore } from 'pinia';

const useMetaboxSettingModalStore = defineStore('metaboxSettingModalStore', {
    state: () => ({
        layout: {
            content: {
                margin: {
                    top: "0",
                    right: "0",
                    bottom: "0",
                    left: "0",
                    sizing: "rem"
                },
                padding: {
                    top: "1",
                    right: "1",
                    bottom: "1",
                    left: "1",
                    sizing: "rem"
                }
            },
            overlay: {
                color: "",
                opacity: "0.5"
            }
        },
        dataAttributes: {},
    }),
    getters: {

    },
    actions: {

    },
});

export default useMetaboxSettingModalStore;
