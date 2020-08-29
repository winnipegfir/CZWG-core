
@extends('layouts.master')
@extends('layouts.cbt')
@section('content')

<div class="container">
<div align="center">
  <br>
  <h1>Exam: {{$subject->name}} </h1>

@foreach($questions as $question)
    <div class="jumbotron" id="jumbotron{{$question->id}}"
            @if($question->id != $current_question_id)
                style="display: none;"
            @endif
            >
        <p>Question #{{$question->id}}</p>
        <p>{{$question->question}}</p>

        {!! Form::open(['action'=>['AtcTraining\CBTController@saveAnswer', $subject->id], 'method'=>'post', 'id'=>'frm'.$question->id]) !!}

        <ul id="answer-radio{{$question->id}}">



                    <label><p>
                        <input type="radio" name="option" value="1"> {{$question->option1}}
                    </label></p>


                        <label><p>
                            <input type="radio" name="option" value="2"> {{$question->option2}}
                        </label></p>

@if ($question->option3 != null)
                        <label><p>
                            <input type="radio" name="option" value="3"> {{$question->option3}}
                        </label></p>
@endif
@if ($question->option4 != null)
                        <label>
                            <input type="radio" name="option" value="4"> {{$question->option4}}
                        </label>
@endif

        <input type="hidden" name="question_id" value="{{$question->id}}">


        </ul>

      {{--  {!! Form::input('hidden','question_id', $question->id) !!}
        {!! Form::input('hidden','time_taken'.$question->id,null,['id'=>'time_taken'.$question->id]) !!}

        {!! Form::token() !!} --}}
@csrf

    @if($question->id != $first_question_id)

        @endif


        @if($question->id == $last_question_id)
        {!! Form::submit('Last', ['class'=>'btn btn-info']) !!}
      <input type="submit" value="Submit Exam">

        @else
          {!! Form::submit('Next', ['class'=>'btn btn-info']) !!}
          <input type="submit" id="Next" value="Next Question">
            @endif
{!! Form::close() !!}
    </div>

    @if($questions->count()>1)
@section('script_form')
    $(function() {

    //console.log({{$question->id}});
    //console.log({{$last_question_id}});

    $('#frm{!!$question->id!!}').on('submit', function(e){
    e.preventDefault();
            var form = $(this);
            var $formAction = form.attr('action');

            var $userAnswer = $('input[name=option]:checked', $('#frm{{$question->id}}')).val();


            $.post($formAction, $(this).serialize(), function(data){

                    //if(data.next_question_id != null)
                        $('#jumbotron{{$question->id}}').hide();
                        //console.log(data.next_question_id);
                        $('#jumbotron' + data.next_question_id+'').show();
           });



        });
    });

    });
@stop
@endif
@endforeach



@stop
