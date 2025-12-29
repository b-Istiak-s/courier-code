<!DOCTYPE html>
<html>
<head>
    <style>
        * {
            margin: 2px;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 7px;
            width: 2.75in;
            height: 2.75in;
            margin: 0;
            padding: 0;
            /* overflow: hidden; */
        }
        .label {
            border: 1px solid #000;
            padding: 3px;
            width: 100%;
            /* max-height: 3in; */
            /* overflow: hidden; */
        }
        .merchant-logo {
            width: 26px;
            height: 26px;
            object-fit: contain;
        }
        .packer-logo {
            width: 32px;
            height: 26px;
            object-fit: contain;
        }
        .store-name {
            font-weight: bold;
            font-size: 8px;
            line-height: 1.1;
        }
        .barcode {
            text-align: center;
            margin: 1px 0;
            height: 22px;
            overflow: hidden;
        }
        .recipient-name {
            font-weight: bold;
            font-size: 8px;
        }
        .cod {
            background: #000;
            color: #fff;
            padding: 1px 2px;
            font-weight: bold;
            font-size: 6px;
        }
        .small {
            font-size: 6px;
            line-height: 1.1;
        }
    </style>
</head>
<body>

<div class="label">

    <!-- HEADER -->
    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="22%" valign="top">
                @if($booking->Merchant->image)
                    <img src="{{ public_path($booking->Merchant->image) }}" class="merchant-logo">
                @else
                    <img src="{{ public_path('logo.jpeg') }}" class="merchant-logo">
                @endif
            </td>

            <td width="56%" valign="top" style="padding:0 2px;">
                <div class="store-name">{{ Str::limit($booking->store->name, 22) }}</div>
                <div class="small">{{ Str::limit($booking->store->address ?? 'N/A', 35) }}</div>
                <div class="small">{{ $booking->store->primary_phone ?? 'N/A' }}</div>
            </td>

            <td width="22%" valign="top" align="right">
                <img src="{{ public_path('Packer_Panda-03.svg') }}" class="packer-logo">
            </td>
        </tr>
    </table>

    <!-- BARCODE -->
    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td class="barcode">
                {{-- Adjusted barcode size for 3x3 inch: width=0.9, height=28 --}}
                {!! DNS1D::getBarcodeHTML($booking->order_id, 'C128', 0.9, 28) !!}
            </td>
        </tr>
    </table>

    <!-- RECIPIENT INFO -->
    <table width="100%" cellspacing="0" cellpadding="0" style="margin-top:3px;">
        <tr>
            <td class="recipient-name">
                {{ Str::limit($booking->recipient_name, 28) }}
            </td>
        </tr>
        <tr>
            <td style="padding-top:1px;">
                <span class="small">{{ $booking->recipient_phone }}</span>
                &nbsp;
                <span class="cod">
                    COD: ৳{{ number_format($booking->amount_to_collect ?? 0) }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="small" style="padding-top:2px;">
                Order: {{ $booking->order_id }}
            </td>
        </tr>
        <tr>
            <td class="small" style="padding-top:2px;">
                {{ Str::limit($booking->recipient_address, 70) }}
            </td>
        </tr>
    </table>

</div>

</body>
</html>
