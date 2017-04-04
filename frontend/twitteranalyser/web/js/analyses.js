var checkboxes = $('.form-check');
var enableComparisonButton = $('#enableComparisonButton');
var compareButton = $('#compareButton');
var comparisonBarText = $('#comparisonBarText');
var toggleCompareButton = function () {
    if ($('.form-check-input:checked').length >= 2) {
        compareButton.prop('disabled', false);
    } else {
        compareButton.prop('disabled', true);
    }
};
var updateNavBarText = function () {
    var numCheckedBoxes = $('.form-check-input:checked').length;
    var textForNavBarText = numCheckedBoxes < 2
        ? 'You must select at least two items'
        : 'Comparing ' + numCheckedBoxes + ' items...';
    comparisonBarText.text(textForNavBarText);
};


enableComparisonButton.click(function() {
    checkboxes.toggle();
    if (enableComparisonButton.text() === 'Enable comparison') {
        enableComparisonButton.text('Disable comparison');
        updateNavBarText();
        toggleCompareButton();
    } else {
        enableComparisonButton.text('Enable comparison');
        comparisonBarText.text('');
        compareButton.prop('disabled', true);
    }
});

checkboxes.change(function () {
    toggleCompareButton();
    updateNavBarText();
});