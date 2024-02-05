<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @font-face{
            font-family:"Inter";
            src: url({$assets_path}'assets/Inter/Inter-Regular.eot');
            src: url({$assets_path}'assets/Inter/Inter-Regular.eot?#iefix') format('embedded-opentype'),
        url({$assets_path}'assets/Inter/Inter-Regular.woff2') format('woff2'),
        url({$assets_path}'assets/Inter/Inter-Regular.woff') format('woff'),
        url({$assets_path}'assets/Inter/Inter-Regular.ttf') format('truetype');
            font-style:normal;
            font-weight: 400;
            font-display:swap;
        }

        @font-face{
            font-family:"Inter";
            src: url({$assets_path}'assets/Inter/Inter-Bold.eot');
            src: url({$assets_path}'assets/Inter/Inter-Bold.eot?#iefix') format('embedded-opentype'),
        url({$assets_path}'assets/Inter/Inter-Bold.woff2') format('woff2'),
        url({$assets_path}'assets/Inter/Inter-Bold.woff') format('woff'),
        url({$assets_path}'assets/Inter/Inter-Bold.ttf') format('truetype');
            font-style:normal;
            font-weight: 700;
            font-display:swap;
        }
        .qr{
            width: 100%;
            max-width: 520px;
            display: flex;
            flex-direction: column;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%,-50%);
            padding: 40px 60px;
            background-color: #ffffff;
            border-radius: 24px;
            color: #121212;
            font-family: Inter;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            letter-spacing: -0.32px;
            border: 1px solid #cfcfcf;
        }
        .qr__flex{
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .qr__outline svg{
            display: none;
        }
        p{
            margin-top: 0;
            margin-bottom: 0;
        }
        .qr__title{
            font-family: Inter;
            font-size: 28px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            letter-spacing: -0.56px;
            margin-top: 0;
            margin-bottom: 0;
        }
        .qr__title span{
            color: #00CE7C;
        }
        .qr__code{
            width: 230px;
            height: 230px;
            align-self: center;
        }
        .qr__code img{
            width: 100%;
            max-width: 100%;
            height: 100%;
        }
        .qr__link{
            display: flex;
            align-items: center;
            justify-content: center;
            outline: none;
            border: 1px solid transparent;
            background-color: #00CE7C;
            border-radius: 20px;
            margin-top: 20px;
            height: 35px;
            color: #fff;
            max-width: 240px;
            align-self: center;
            width: 100%;
            text-decoration: none;
            transition: .3s all;
        }
        .qr__link:hover{
            background: #fff;
            color: #00CE7C;
            border-color: #00CE7C;
        }
        .qr__link_mobile{
            display: none;
        }
        .qr__link_desktop {
            display: flex;
        }
        .qr__info{
            margin-top: 40px;
        }
        .qr__text:not(:first-of-type){
            margin-top: 14px;
        }
        .qr__outline{
            border-radius: 20px;
            border: 1px solid #00CE7C;
            height: 27px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 8px;
            text-transform: uppercase;
        }
        .qr__outline #address{
            text-transform: none;
        }
        .qr__timer{
            margin-top: 20px;
        }
        .qr__timer p{
            display: flex;
            align-items: center;
        }
        .qr__timer span{
            margin-left: 10px;
        }
        .qr__time-remain{
            border-radius: 20px;
            border: 1px solid #00CE7C;
            text-align: center;
            padding: 4px 12px;
        }
        .qr__progress{
            border-radius: 10px;
            background: #EEE;
            height: 5px;
            margin-top: 6px;
            position: relative;
        }
        .qr__progress-bar{
            background-color: #00CE7C;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            width: 100%;
            transition: width 1s;
            transition-timing-function: linear;
            border-radius: 10px;
        }
        @media(max-width: 768px){
            .qr{
                max-width: 400px;
                padding: 32px 44px;
            }
        }
        @media(max-width: 500px){
            .qr{
                padding: 10px;
                border: none;
                max-width: calc(100% -  20px);
                top: 0;
                transform: translate(-50%, 0);
            }
            .qr__text:not(:first-of-type){
                margin-top: 10px;
            }
            .qr__progress{
                height: 14px;
                border-radius: 20px;
            }
            .qr__progress-bar{
                border-radius: 20px;
            }
            .qr__outline{
                font-weight: 600;
            }
            .qr__outline p{
                overflow: hidden;
                max-width: 228px;
                text-overflow: ellipsis;
            }
            .qr__outline svg{
                display: block;
                margin-left: 10px;
            }
            .qr__link{
                display: none;
            }
            .qr__link_mobile{
                display: flex;
            }
            .qr__link_desktop {
                display: flex;
            }
        }
    </style>
    <title>Payment via Green Crypto Processing</title>
</head>
<body>
<div class="qr">


    <div class="qr__flex">
        <h2 class="qr__title">Order <span>#{$order_id}</span></h2>
    </div>

    <div id="qrcode" class="qr__code"></div>

    {if !empty($wallet_link)}
        <a href="{$wallet_link}" class="qr__link" target="_blank">Open wallet</a>
    {/if}

    <div class="qr__info">
        <p class="qr__text">To pay, send exactly this {$payment_method} amount</p>
        <p class="qr__outline">{$amount} {$payment_method} = {$total} {$currency}</p>
        <p class="qr__text">To this {$payment_method} address</p>

        <div class="qr__outline">
            <p id="address" data-payment-address="{$payment_address}">{$payment_address}</p>
            <svg id="copy" xmlns="http://www.w3.org/2000/svg" width="16" height="18" viewBox="0 0 16 18" fill="none">
                <g clip-path="url(#clip0_64_2332)">
                    <path d="M13.3571 11.8125H6.92857C6.63393 11.8125 6.39286 11.5594 6.39286 11.25V2.25C6.39286 1.94062 6.63393 1.6875 6.92857 1.6875H11.6194L13.8929 4.07461V11.25C13.8929 11.5594 13.6518 11.8125 13.3571 11.8125ZM6.92857 13.5H13.3571C14.5391 13.5 15.5 12.491 15.5 11.25V4.07461C15.5 3.62812 15.3292 3.19922 15.0279 2.88281L12.7578 0.495703C12.4565 0.179297 12.048 0 11.6228 0H6.92857C5.74665 0 4.78571 1.00898 4.78571 2.25V11.25C4.78571 12.491 5.74665 13.5 6.92857 13.5ZM2.64286 4.5C1.46094 4.5 0.5 5.50898 0.5 6.75V15.75C0.5 16.991 1.46094 18 2.64286 18H9.07143C10.2533 18 11.2143 16.991 11.2143 15.75V14.625H9.60714V15.75C9.60714 16.0594 9.36607 16.3125 9.07143 16.3125H2.64286C2.34821 16.3125 2.10714 16.0594 2.10714 15.75V6.75C2.10714 6.44063 2.34821 6.1875 2.64286 6.1875H3.71429V4.5H2.64286Z" fill="#121212"/>
                </g>
                <defs>
                    <clipPath id="clip0_64_2332">
                        <rect width="15" height="18" fill="white" transform="translate(0.5)"/>
                    </clipPath>
                </defs>
            </svg>
        </div>
    </div>
    <div class="qr__timer">
        <p>Time to pay <span class="qr__time-remain" id="progress-remain" data-time-to-pay="{$time_to_pay}"></span></p>
        <div class="qr__progress">
            <div id="progress" class="qr__progress-bar"></div>
        </div>
    </div>
    <br>

    {if !empty($wallet_link)}
        <a href="{$wallet_link}" class="qr__link qr__link_mobile" target="_blank">Open wallet</a>
    {/if}

    <a href="/" class="qr__link qr__link_desktop">Back to site</a>
</div>
</body>
<script src="{$assets_path}jquery.min.js"></script>
<script src="{$assets_path}qrcode.min.js"></script>
<script src="{$assets_path}script.js"></script>

</html>
