    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Member Card</title>
        <script src="https://cdn.jsdelivr.net/npm/dom-to-image@2.6.0/dist/dom-to-image.min.js"></script>
    </head>
    <body>
<div id="memberCard" class="member-card-wrapper">
    <div class="member-card">
        <div class="card-body">
            <div class="member-photo">
                <img src="{{ asset('storage/' . $foto->image) }}" alt="{{ $foto }}">
            </div>
            <div class="valid-until">
                <span>Disahkan tanggal:</span><br>
                <strong>{{ $profile->created_at->translatedFormat('d F Y') }}</strong>     </div>
        </div>

        <div class="col-md-8 member-info">
            <div class="member-details">
                <strong>{{ $profile->name }}</strong><br>
                <strong>{{ $profile->nomember }}</strong>
            </div>
        </div>

        <div class="qr-code">
            <div> {{ $qrCode }}</div>

        </div>
    </div>
</div>


    <button id="downloadBtn" style="margin-top: 20px; padding: 10px 20px; font-size: 16px; cursor: pointer;">Download as PNG</button>

    <style>

.member-card-wrapper {
    display: inline-block; /* Mengatur wrapper agar pas dengan konten */
    padding: 1px; /* Padding di luar elemen */
    background-color: #000; /* Warna latar pembungkus */
    border-radius: 40px; /* Menambahkan efek lengkungan */
}
        .member-card {
            position: relative;
            width: 591px;
            height: 1004px;
            box-shadow: 0px 5px 5px 5px rgba(0, 0, 0, 0.5);
            background: url('/assets/images/bg-card-2.png') no-repeat center;
            background-size: cover;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            overflow: hidden;
            padding: 20px;
            border-radius: 40px;
        }

        .card-body {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            width: 100%;
            padding: 10px;
        }

        .member-photo {
            position: absolute;
            width: 480px;
            height: 640px; /* Set a fixed height to ensure consistent placement */
            margin: 0;
            padding: 0;
            top: 245px;
            overflow: hidden; /* Ensure the image fits within the container */
        }

        .member-photo img {
            width: 100%;
            height: 100%; /* Ensure the image covers the entire container */
            object-fit: cover; /* Maintain aspect ratio and cover the container */
            border-radius: 10px;
        }

        .valid-until {
            position: absolute;
            top: 230px;
            right: 10px;
            font-size: 18px;
            color: rgb(10, 2, 56);
            writing-mode: vertical-rl;
            text-orientation: mixed;
            text-align: left;
            white-space: nowrap;
        }

        .member-info {
            text-align: center;
            margin-top: 20px;
        }

        .member-details {
            text-align: center;
            margin: 0;
            padding: 0;
            width: 100%;
            position: absolute;
            font-size: 24px;
            right: 0;
            bottom: 7%;
            color: #000000;
        }

        .qr-code {
            display: flex;
            position: absolute;
            right: 11.9%;
            bottom: 13.3%;
        }

        .qr-code div {
            width: 150px;
            height: 150px;
            border: 2px solid #ccc;
            box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 0.8);
            border-radius: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const memberCard = document.getElementById('memberCard');

            // Render DOM element to PNG using dom-to-image
            domtoimage.toPng(memberCard)
                .then(dataUrl => {
                    // Create a link element to download the image
                    const link = document.createElement('a');
                    link.download = 'Kartu Anggota - {{ $profile->name }}.png';
                    link.href = dataUrl;
                    link.click();

                    // Send the image data to the server to save it
                    fetch('/user/member-card/save-member-card', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ image: dataUrl })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Image saved successfully:', data);
                    })
                    .catch(error => {
                        console.error('Error saving image:', error);
                    });
                })
                .catch(error => {
                    console.error('Error rendering image:', error);
                });
        });
    </script>
    </body>
    </html>
