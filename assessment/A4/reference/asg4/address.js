(function() {
    var widget, initAddressFinder = function() {
        widget = new AddressFinder.Widget(
            document.getElementById('Address'),
            'ADDRESSFINDER_DEMO_KEY',
            'AU', {
                "address_params": {
                    "gnaf": "1"
                }
            }
        );

        widget.on('address:select', function(fullAddress, metaData) {
            document.getElementById('Address').value = metaData.address_line_1;
            document.getElementById('ApartmentNo').value = metaData.address_line_2;
            document.getElementById('Suburb').value = metaData.locality_name;
            document.getElementById('State').value = metaData.state_territory;
            document.getElementById('Postcode').value = metaData.postcode;

        });


    };

    function downloadAddressFinder() {
        var script = document.createElement('script');
        script.src = 'https://api.addressfinder.io/assets/v3/widget.js';
        script.async = true;
        script.onload = initAddressFinder;
        document.body.appendChild(script);
    };

    document.addEventListener('DOMContentLoaded', downloadAddressFinder);
})();
