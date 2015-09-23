form xml Soundfile
  sentence input
  sentence output
endform

if fileReadable (output$)
  filedelete 'output$'
endif

minpitch = 75
shift = 0.05
datetime$ = date$()

fileappend 'output$' <?xml version="1.0" encoding="utf-8" ?> 'newline$'

text$ = "<results> 'newline$'"
text$ = text$ + "  <date>'datetime$'</date> 'newline$'"

Read from file... 'input$'
name$ = selected$ ("Sound")
To Intensity... minpitch shift
select Sound 'name$'
To Pitch (ac)... shift minpitch 15 no 0.03 0.45 0.01 0.35 0.14 600

select Intensity 'name$'
nrframes = Get number of frames
select Pitch 'name$'
for framenr from 1 to nrframes
  frametime = framenr * shift
  intensityvalue = Intensity_'name$' [framenr]
  if intensityvalue < 0
    intensityvalue = 0
  endif
  pitchvalue = Get value in frame... framenr Hertz
  if pitchvalue = undefined or pitchvalue <= 0
    pitchvalue = 0
  endif
  text$ = text$ + "  <res> 'newline$'"
  text$ = text$ + "    <time>'frametime:2'</time> 'newline$'"
  text$ = text$ + "    <int>'intensityvalue:0'</int> 'newline$'"
  text$ = text$ + "    <pit>'pitchvalue:0'</pit> 'newline$'"
  text$ = text$ + "  </res> 'newline$'"
endfor
text$ = text$ + "</results> 'newline$'"
text$ >> 'output$'

select Sound 'name$'
plus Intensity 'name$'
plus Pitch 'name$'
Remove
