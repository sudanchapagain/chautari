
var eventId = <?= $event['event_id'] ?>;
var productName = "<?= $event['title'] ?>";
var eventPrice = <?= $event['ticket_price'] ?>;

if (eventPrice > 0) {
    var config = {
        "publicKey": "46b35070613c4b79853d998afc3feafa",
        "productIdentity": eventId,
        "productName": productName,
        "productUrl": "http://localhost:8080/event?event_id=" + eventId,
        "eventHandler": {
            onSuccess (payload) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/event?event_id=<?= $event_id ?>&action=pay';

                var tokenInput = document.createElement('input');
                tokenInput.name = 'token';
                tokenInput.value = payload.token;

                form.appendChild(tokenInput);
                document.body.appendChild(form);
                form.submit();
            },
            onError (error) {
                console.error(error);
            },
            onClose () {
                console.log("Payment window closed");
            }
        }
    };
    var checkout = new KhaltiCheckout(config);
    document.getElementById('pay-button').onclick = function () {
        checkout.show({amount: eventPrice * 100});
    };
} else {
    document.getElementById('pay-button').addEventListener('click', function (e) {
        e.preventDefault();
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '/event?event_id=<?= $event_id ?>&action=pay';
        document.body.appendChild(form);
        form.submit();
    });
}
