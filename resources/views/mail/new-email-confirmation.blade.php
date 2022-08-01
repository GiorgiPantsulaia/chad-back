<div
    style="
        background: linear-gradient(
            187.16deg,
            #181623 0.07%,
            #191725 51.65%,
            #0d0b14 98.75%
        );
        width: 100%;
        height:auto 100%;
        padding-bottom: 15px;
    "
>
    <div
        style="
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 100px;
            justify-content: center;
            width: 30%;
            margin: auto;
        "
    >
        <img src="https://i.ibb.co/CBZJYZX/Vector.png" alt="quote"  style="width: 40px; height: 30px;margin-top: 10px" />
        <h2
            style="
                color: #ddccaa;
                font-family: Arial, Helvetica, sans-serif;
                font-weight: 200;
            "
        >
            MOVIE QUOTES
        </h2>
    </div>
    <div style="margin-left: 10%; margin-top: 5%; color: #fff; ">
        Hola, <b>{{ $email_data['name'] }}</b> <br /><br />

        <br /><br />
        <h3 style="font-family: Arial, Helvetica, sans-serif; font-weight: 200">
          Click the button below to confirm and change the email address for your account
        </h3>
        <br />
        <a
            href="{{ env('FRONT_REDIRECT') }}/profile?token={{ $email_data['verification_code'] }}&email={{ $email_data['email'] }}"
            ><button
                style="
                    background-color: #e31221;
                    width: 250px;
                    height: 56px;
                    color: white;
                    font-size: x-large;
                    border: none;
                    border-radius: 4px;
                    font-weight: bold;
                    cursor: pointer;
                "
            >
               Confirm
            </button></a
        >
        <br /><br />
        <h3 style="font-family: Arial, Helvetica, sans-serif; font-weight: 200">
            If clicking doesn't work, you can try copying and pasting it to your
            browser:
        </h3>

        <p
            style="
                color: #ddccaa;
                font-family: Arial, Helvetica, sans-serif;
                font-weight: 200;
                word-wrap: break-word;
            "
        >
            http://localhost:3000/profile?token={{
            $email_data['verification_code'] }}&email={{ $email_data['email'] }}
        </p>
        <br />
        <h4 style="font-weight: 100; font-family: Arial, Helvetica, sans-serif">
            If you have any problems, please contact us: support@moviequotes.ge
        </h4>
        MovieQuotes Crew
        <br>
    </div>
</div>
