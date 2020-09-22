import Vue from 'vue'

Vue.filter('milliseconds', function (value) {
    let ms = Math.floor(value / 1000000);

    if (ms === 0) {
        return "< 1ms";
    }

    return ms + "ms";
})