document.getElementById('addBookForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const kodeBuku = document.getElementById('kodeBuku').value;
    const judulBuku = document.getElementById('judulBuku').value;
    const kerusakan = document.getElementById('kerusakan').value;
    const kehilangan = document.getElementById('kehilangan').value;
    const urgensi = document.getElementById('urgensi').value;

    fetch('process.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'add_book',
            kode_buku: kodeBuku,
            judul_buku: judulBuku,
            kerusakan: kerusakan,
            kehilangan: kehilangan,
            urgensi: urgensi
        })
    })
    .then(response => response.json())
    .then(data => alert(data.message))
    .catch(error => console.error('Error:', error));
});

document.getElementById('calculateButton').addEventListener('click', function () {
    fetch('process.php?action=calculate_saw')
        .then(response => response.json())
        .then(data => {
            const resultTable = document.getElementById('resultTable').querySelector('tbody');
            resultTable.innerHTML = ''; // Kosongkan tabel sebelumnya

            data.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.kode_buku}</td>
                    <td>${row.judul_buku}</td>
                    <td>${row.saw_value}</td>
                    <td>${row.explanation}</td> <!-- Kolom Rekomendasi -->
                `;
                resultTable.appendChild(tr);
            });
        })
        .catch(error => console.error('Error:', error));
});
