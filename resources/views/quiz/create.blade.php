<form action='{{ route('quiz.store') }}' method="POST">

    @csrf

    <input type="datetime-local" name="start_at" required>
    <input type="number" min="0" name="duration" required>

    <hr>
    <hr>

    @for ($i = 1; $i < 5; $i++)
        <label for="questions[{{ $i }}][content]">
            Question
        </label>
        <textarea name="questions[{{ $i }}][content]" cols="30" rows="10" required></textarea>


        <hr>

        @for ($j = 1; $j < 5; $j++)
            <label for="questions[{{ $i }}][choices][{{ $j }}]">
                Choix {{ $j }}
            </label>
            <input type="radio" name="questions[{{ $i }}][is_correct]" value="{{ $j }}" required>
            <textarea name="questions[{{ $i }}][choices][{{ $j }}]" cols="30" rows="8"></textarea>
        @endfor

        <hr>
        <hr>
    @endfor

    <button type="submit"> Ajouter </button>

</form>
