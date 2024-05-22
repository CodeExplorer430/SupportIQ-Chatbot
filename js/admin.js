$(document).ready(function() {
    let responses = [];

    // Fetch responses from JSON file
    $.getJSON('../data/responses.json', function(data) {
        responses = data;
        loadResponses();
    });

    // Load responses into the list
    function loadResponses() {
        const responsesList = $('#responsesList');
        responsesList.empty();
        responses.forEach((response, index) => {
            responsesList.append(`<li class="list-group-item" data-index="${index}">${response.text}</li>`);
        });
    }

    // Handle response click
    $('#responsesList').on('click', '.list-group-item', function() {
        const index = $(this).data('index');
        const response = responses[index];
        $('#responseId').val(response.id);
        $('#responseText').val(response.text);
    });

    // Handle form submission
    $('#updateForm').on('submit', function(e) {
        e.preventDefault();
        const responseId = $('#responseId').val();
        const responseText = $('#responseText').val();
        const index = responses.findIndex(response => response.id === responseId);

        if (index !== -1) {
            responses[index].text = responseText;
            loadResponses();
            alert('Response updated successfully!');
        } else {
            alert('Response not found!');
        }
    });
});
