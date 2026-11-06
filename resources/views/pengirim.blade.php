<form id="kirimForm" method="post" action="{{ route('kirim.now') }}">
    @csrf
    <button id="kirimBtn" type="button">Kirim Sekarang</button>
</form>

<div id="status"></div>

<script>
document.getElementById('kirimBtn').addEventListener('click', async () => {
  const token = document.querySelector('input[name="_token"]').value;
  document.getElementById('status').innerText = 'Mengirim...';

  try {
    const res = await fetch('{{ route("kirim.now") }}', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
    });
    const json = await res.json();
    document.getElementById('status').innerText = json.ok ? json.message : 'Gagal';
  } catch (err) {
    document.getElementById('status').innerText = 'Error: ' + err;
  }
});
</script>
