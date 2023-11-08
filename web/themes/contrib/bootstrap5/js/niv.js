(function (Drupal, $, once) {
    Drupal.behaviors.myBehavior = {
    
    
        attach: function (context, settings) {


          var current = location.pathname;
              $('.registration-links  a').each(function(){
              var $this = $(this);
              // if the current path is like this link, make it active
              if($this.attr('href').indexOf(current) !== -1){
                  $this.addClass('active');
              }
          });

          $('select#edit-field-section-attribute').on('change',function(){

            var attribute =  $('#edit-field-section-attribute').val();
            
            $('#edit-field-selected-attribute-value-0-value').val(attribute) ;

          });
          $('select#edit-field-form-type,select#edit-field-suggestion-section').on('change',function(){
            
            var section = $('#edit-field-suggestion-section').val();
            var formtype = $('#edit-field-form-type').val();
            //alert(formtype);
            //alert(section);
            if(section !='_none' && formtype != '_none'){

              $('#edit-field-section-attribute').attr('disabled','disabled');

            $.ajax({
              type: "GET",
              url: '/getAttributes/'+formtype+'/'+section,
                         
              dataType: 'json',
              success : function(result){
                
                if(result){
                  //var obj = jQuery.parseJSON(result);
                  $('#edit-field-section-attribute options').remove();
                  $('#edit-field-section-attribute').html(new Option('- Select a value -', '_none'));
                  $.each(result, function(key,value) {
                    $('#edit-field-section-attribute').append(new Option(value, key));
                    $('#edit-field-section-attribute').removeAttr('disabled');
                    
                  }); 
                  
                  //$('#edit-field-section-attribute').
                }
                else{
                 
                  
                }
                
                
              }
            });
          }

          
           
        });
      



          function getPerformanceIndex(value){

        
            
            
            
            var value = value;
            
            var config = {
              type: 'gauge',
              data: {
                //labels: ['Success', 'Warning', 'Warning', 'Error'],
                datasets: [{
                  data: [412,525,638,751,800],
                  value: value,
                  minValue: 0,
                  backgroundColor: ['#FF0000', '#FF4500', '#FFA500', '#90EE90','#2E8B57'],
                  borderWidth: 2
                }]
              },
              options: {
            
                labels: {
                                // This more specific font property overrides the global property
                                font: {
                                    size: 20
                                }
                            },
                responsive: true,
                maintainAspectRatio: true,
                title: {
                  display: true,
                  text: 'Performance Index'
                },
                layout: {
                  padding: {
                    bottom: 30
                  }
                },
                needle: {
                  // Needle circle radius as the percentage of the chart area width
                  radiusPercentage: 2,
                  // Needle width as the percentage of the chart area width
                  widthPercentage: 3.2,
                  // Needle length as the percentage of the interval between inner radius (0%) and outer radius (100%) of the arc
                  lengthPercentage: 80,
                  // The color of the needle
                  color: 'rgba(0, 0, 0, 1)'
                },
                valueLabel: {
                  formatter: Math.round,
                  color: 'rgba(255, 255, 255, 1)',
                  backgroundColor: 'rgba(0, 0, 0, 1)',
                  borderRadius: 5,
                  padding: {
                    top: 10,
                    bottom: 10
                  },
                 
            
                }
              }
            };
            
          //  window.onload = function() {
              var ctx = document.getElementById('chart').getContext('2d');
              window.myGauge = new Chart(ctx, config);
           // };



          }

          
        }
      };
    
    }(Drupal, jQuery, once));
      