<a href="{{ route('master.currencies.edit', $currency) }}" class="btn btn-sm btn-warning">Edit</a>
<form action="{{ route('master.currencies.destroy', $currency) }}" method="POST" style="display: inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
</form>