HTTP API Reference
==================

Overview
--------

Our product environment's host is www.pilibaba.com. We strongly suggest your visit it via HTTPS for security.

Submit Order Api <span id="-submit-order-api"></span>
-----------------------------------------------------

Path: /pilipay/payreq Request Type: POST

### Parameters

| Field | M/O | Type | Description |
|-------|-----|------|-------------|

Update Track Number
-------------------

Path: /pilipay/updateTrackNo Request Type: GET Function: Update the track number of the specified order after parcels are sent.

### Parameters

| Field       | M/O |    Type| Description                                                                                                                                                                          |
|-------------|:---:|-------:|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| orderNo     |  M  |  String| The order number, created from your site, identifies the order. It should has been pushed to pilibaba via the [submit order API](#submit-order-api).                                 |
| logisticsNo |  M  |  String| The logistics number, provided by the express company after parcels are sent, is used to track the parcels.                                                                          |
| merchantNo  |  M  |  String| The merchant number, provided by pilibaba after [signed up](http://www.pilibaba.com/en/regist), can be got from your [member info page](http://pilibaba.com/en/account/member-info). |

### Response

The response of this interface can be ignored.

### Example

    https://www.pilibaba.com/pilipay/updateTrackNo?orderNo=123&logisticsNo=12345678&merchantNo=0210001234

### Warehouse Information

We have established serveral warehouses around the world: USA、Austria(Italy)、UK warehouse and so on. For detailed list, please visit <http://www.pilibaba.com/en/addressList>.
