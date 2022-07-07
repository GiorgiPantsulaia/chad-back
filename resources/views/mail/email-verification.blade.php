<div
    style="
        background: linear-gradient(
            187.16deg,
            #181623 0.07%,
            #191725 51.65%,
            #0d0b14 98.75%
        );
        width: 100%;
        height: 100%;
    "
>
    <div
        style="
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 100px;
        "
    >
        <img src="https://i.ibb.co/CBZJYZX/Vector.png" alt="quote" width="30" />
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
    <div style="margin-left: 15%; margin-top: 5%; color: #fff">
        Hola, <b>{{ $email_data['name'] }}</b> <br /><br />

        <br /><br />
        <h3 style="font-family: Arial, Helvetica, sans-serif; font-weight: 200">
            Thanks for joining Movie quotes! We really appreciate it. Please
            click the button below to verify your account:
        </h3>
        <br />
        <a
            href="{{ env('FRONT_REDIRECT') }}/verify?token={{ $email_data['verification_code'] }}"
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
                Verify Account
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
            "
        >
            http://localhost:3000/verify?token={{
            $email_data['verification_code'] }}
        </p>
        <br />
        <h4 style="font-weight: 100; font-family: Arial, Helvetica, sans-serif">
            If you have any problems, please contact us: support@moviequotes.ge
        </h4>
        MovieQuotes Crew
    </div>
</div>
