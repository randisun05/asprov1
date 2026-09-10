import sys
import fitz  # PyMuPDF
import os
import io
import qrcode

def generate_qr_bytes(data):
    """
    Menghasilkan bytes PNG QR Code dengan resolusi tinggi.
    Menggunakan box_size besar agar gambar sumber tajam.
    """
    qr = qrcode.QRCode(
        version=1,
        error_correction=qrcode.constants.ERROR_CORRECT_H,
        box_size=20,  # Resolusi tinggi (sekitar 600px - 1000px)
        border=1,
    )
    qr.add_data(data)
    qr.make(fit=True)

    img = qr.make_image(fill_color="black", back_color="white")
    img_bytes = io.BytesIO()
    img.save(img_bytes, format="PNG")
    return img_bytes.getvalue()

def main():
    if len(sys.argv) < 2:
        print("Usage: python generate_certificate.py <pdf_template_path> key=value ...")
        sys.exit(1)

    pdf_template_path = sys.argv[1]
    # maxsplit=1: a certificate holder's name or category can legitimately
    # contain '=' - splitting on every '=' would raise "too many values to
    # unpack" and crash the whole script instead of just parsing the value
    # as everything after the first '='.
    args = {key: value for key, value in (arg.split('=', 1) for arg in sys.argv[2:])}

    data = {
        "#nomor": args.get("nomor", ""),
        "#nama": args.get("nama", ""),
        "#qr": args.get("qr", ""),
        "$file": args.get("file", "sertifikat.pdf"),
        "$path": args.get("path", "."),
    }

    doc = fitz.open(pdf_template_path)
    page = doc[0]

    # --- Bagian Penyisipan QR Code ---
    if data["#qr"]:
        qr_areas = page.search_for("#qr")
        if qr_areas:
            for area in qr_areas:
                # Hapus teks anchor #qr (Redaction)
                page.add_redact_annot(area, fill=(1, 1, 1))
                page.apply_redactions()

                # Generate QR HD dalam bentuk Bytes
                qr_data_bytes = generate_qr_bytes(data["#qr"])

                # Ukuran display di PDF (dalam point)
                qr_size = 80

                # Hitung posisi tengah berdasarkan anchor
                center_x = (area.x0 + area.x1) / 2
                center_y = (area.y0 + area.y1) / 2

                qr_rect = fitz.Rect(
                    center_x - qr_size / 2,
                    center_y - qr_size / 2,
                    center_x + qr_size / 2,
                    center_y + qr_size / 2
                )

                # Gunakan 'stream' alih-alih 'pixmap' untuk menjaga kualitas
                page.insert_image(qr_rect, stream=qr_data_bytes)

    # --- Bagian Penyisipan Teks ---
    font_settings = {
        "#nomor": {"fontsize": 20, "fontname": "helv", "color": (0, 0, 0), "y_offset": 15},
        "#nama": {"fontsize": 24, "fontname": "helv", "color": (0, 0, 0), "y_offset": 15},
    }

    found_anchors = set()

    for anchor, value in data.items():
        if anchor in ["#qr", "$file", "$path"]:
            continue

        areas = page.search_for(anchor)
        if areas:
            found_anchors.add(anchor)
            for area in areas:
                page.add_redact_annot(area, fill=(1, 1, 1))
                page.apply_redactions()

                settings = font_settings.get(anchor, {})
                fontsize = settings.get("fontsize", 12)
                fontname = settings.get("fontname", "helv")
                color = settings.get("color", (0, 0, 0))
                y_offset = settings.get("y_offset", 10)

                font = fitz.Font(fontname=fontname)
                text_width = font.text_length(value, fontsize=fontsize)

                center_x = (area.x0 + area.x1) / 2
                x = center_x - (text_width / 2)
                y = area.y0 + y_offset

                page.insert_text((x, y), value, fontsize=fontsize, fontname=fontname, color=color)

    # Previously this fell through silently when a template had none of the
    # expected placeholder text (e.g. a corrupted file, or an image
    # mistakenly uploaded as a template) - it would still "successfully"
    # produce a PDF with no certificate number or name on it at all.
    if not found_anchors:
        print("Error: tidak ada placeholder (#nomor/#nama) yang ditemukan di template.", file=sys.stderr)
        sys.exit(1)

    # --- Simpan PDF ---
    output_dir = data.get("$path", ".")
    os.makedirs(output_dir, exist_ok=True)
    # basename() as defense in depth - the PHP caller already sanitizes the
    # filename before it gets here, but a '/' slipping through would
    # otherwise let this write outside output_dir.
    output_path = os.path.join(output_dir, os.path.basename(data["$file"]))

    # Gunakan deflate=True dan garbage=4 untuk optimasi file
    doc.save(output_path, garbage=4, deflate=True, clean=True)
    doc.close()

    print(f"Sertifikat berhasil dibuat: {output_path}")

if __name__ == "__main__":
    main()
