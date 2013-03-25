angular.module('uRemainingTime', []).directive('uRemainingTime', function($timeout) {
    // return the directive link function. (compile function not needed)
    return function(scope, element, attrs) {
        var endTime, // end time in seconds
            timeoutId; // timeoutId, so that we can cancel the time updates

        // used to update the UI
        function updateTime() {
            // Set the unit values in milliseconds.
            var msecPerMinute = 1000 * 60;
            var msecPerHour = msecPerMinute * 60;
            var msecPerDay = msecPerHour * 24;

            // Get the difference in milliseconds.
            var interval = endTime * 1000 - new Date().getTime();

            // Calculate how many days the interval contains. Subtract that
            // many days from the interval to determine the remainder.
            var days = Math.floor(interval / msecPerDay);
            interval = interval - (days * msecPerDay);

            // Calculate the hours, minutes, and seconds.
            var hours = Math.floor(interval / msecPerHour);
            interval = interval - (hours * msecPerHour);

            var minutes = Math.floor(interval / msecPerMinute);
            interval = interval - (minutes * msecPerMinute);

            var seconds = Math.floor(interval / 1000);

            // Display the result.
            element.text((days > 0 ? days + 'd ' : '') + hours + "h:" + minutes + "m:" + seconds + "s");
        }

        // watch the expression, and update the UI on change.
        scope.$watch(attrs.uRemainingTime, function(value) {
            endTime = value;
            updateTime();
        });

        // schedule update in one second
        function updateLater() {
            // save the timeoutId for canceling
            timeoutId = $timeout(function() {
                updateTime(); // update DOM
                updateLater(); // schedule another update
            }, 1000);
        }

        // listen on DOM destroy (removal) event, and cancel the next UI update
        // to prevent updating time ofter the DOM element was removed.
        element.bind('$destroy', function() {
            $timeout.cancel(timeoutId);
        });

        updateLater(); // kick off the UI update process.
    }
});



