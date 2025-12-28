<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            width: 80mm;
            margin: 0;
            padding: 5px;
        }
        .label {
            border: 1px solid #ddd;
            padding: 6px;
        }
        .merchant-logo {
            width: 45px;
            height: 45px;
            object-fit: contain;
        }
        .packer-logo {
            width: 55px;
            height: 45px;
            object-fit: contain;
        }
        .store-name {
            font-weight: bold;
            font-size: 12px;
        }
        .barcode {
            text-align: center;
            margin: 6px 0;
        }
        .recipient-name {
            font-weight: bold;
            font-size: 12px;
        }
        .cod {
            background: #000;
            color: #fff;
            padding: 2px 5px;
            font-weight: bold;
            font-size: 11px;
        }
        .small {
            font-size: 10px;
        }
    </style>
</head>
<body>

<div class="label">

    <!-- HEADER -->
    <table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:8px;">
        <tr>
            <td width="25%" valign="top">
                @if($booking->Merchant->image)
                    <img src="{{ public_path($booking->Merchant->image) }}" class="merchant-logo">
                @else
                    <img src="{{ public_path('logo.jpeg') }}" class="merchant-logo">
                @endif
            </td>

            <td width="50%" valign="top">
                <div class="store-name">{{ $booking->store->name }}</div>
                <div class="small">{{ $booking->store->address ?? 'N/A' }}</div>
                <div class="small">{{ $booking->store->primary_phone ?? 'N/A' }}</div>

                <div class="small" style="font-weight: bold">{{ $booking->pathao_consignment_ids }}</div>
            </td>

            <td width="25%" valign="top" align="right">
                <img src="{{ public_path('Packer_Panda-03.svg') }}" class="packer-logo">
            </td>
        </tr>
    </table>

    <!-- BARCODE -->
    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td class="barcode" style="height:40px; overflow:hidden;">
                {{-- change the width (1.3) based on the format of order id, as there are different unique types of order ids --}}
                {!! DNS1D::getBarcodeHTML($booking->order_id, 'C128', 1.3) !!}
            </td>
        </tr>
    </table>

    <!-- RECIPIENT INFO -->
    <table width="100%" cellspacing="0" cellpadding="0" style="margin-top:6px;">
        <tr>
            <td class="recipient-name">
                {{ $booking->recipient_name }}
            </td>
        </tr>
        <tr>
            <td>
                {{ $booking->recipient_phone }}
                &nbsp;|&nbsp;
                <span class="cod">
                    COD: BDT {{ number_format($booking->amount_to_collect ?? 0, 2) }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="small" style="padding-top:4px;">
                Order ID: {{ $booking->order_id }}
            </td>
        </tr>
        <tr>
            <td class="small" style="padding-top:4px;">
                {{ $booking->recipient_address }}
            </td>
        </tr>
    </table>

</div>

</body>
</html>
