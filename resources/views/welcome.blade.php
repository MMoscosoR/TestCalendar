@extends('layouts.main')
@section('links')
    <link rel="stylesheet" href="/pluggins/fullcalendar/main.css">
@endsection
@section('contenido')
        <div id="calendar"></div>
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="form_horario" onSubmit="sendForm()">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_titulo">dsf</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Fecha</label>
                                <input type="date" class="form-control" name="form_date" id="form_date" required>
                            </div>
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="exampleInputPassword1" class="form-label">Desde</label>
                                        <select class="form-control" name="form_from" id="form_from" required>
                                            @for($i=6;$i<=17;$i++)
                                                <option>{{str_pad($i,2,'0',0)}}:00</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label for="exampleInputPassword1" class="form-label">Hasta</label>
                                        <select class="form-control" name="form_to" id="form_to" required>
                                            @for($i=6;$i<=17;$i++)
                                                <option>{{str_pad($i,2,'0',0)}}:00</option>
                                            @endfor
                                        </select>
                                    </div>

                                </div>

                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Nombre del curso</label>
                                <input type="text" class="form-control" name="nombre_curso" id="form_nombre_curso" required>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Descripción</label>
                                <textarea name="descripcion" rows="3" class="form-control" id="form_descripcion"></textarea>
                            </div>

                    </div>
                    <div class="modal-footer">
                         <button type="button" class="btn btn-danger" onclick="deleteHorario()" id="btn_eliminar">Eliminar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" >Guardar</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
@endsection
@section('scripts')
    <script src="/pluggins/fullcalendar/main.js"></script>
    <script src="/pluggins/fullcalendar/locales/es-us.js"></script>

    <script>
        var formulario = new bootstrap.Modal(document.getElementById('staticBackdrop'))
        var actionId=null;
        let showModalForm=(info,accion)=>{
            document.getElementById('modal_titulo').innerHTML=accion;
            if(actionId){
                $('#btn_eliminar').show();
                console.log('modo edicion',info);
                document.getElementById('form_date').value=getDataFromString(info.event.startStr,'fulldate');
                document.getElementById('form_from').value=getDataFromString(info.event.startStr,'time');
                document.getElementById('form_to').value=getDataFromString(info.event.endStr,'time');
                document.getElementById('form_nombre_curso').value=info.event.title;
                document.getElementById('form_descripcion').value=info.event.extendedProps.description;
            }else{
                $('#btn_eliminar').hide()
                document.getElementById('form_date').value=getDataFromString(info.dateStr,'fulldate');
                document.getElementById('form_from').value=getDataFromString(info.dateStr,'time');
                document.getElementById('form_to').value=getDataFromString(info.dateStr,'timetoDefault');
            }

            formulario.show();
        }

        var calendar;
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                height:700,
                timeZone: 'UTC',
                locale: 'es-us',
                allDaySlot:false,
                expandRows:true,
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    //right: 'timeGridWeek'
                },
                eventMinHeight:'20px',
                slotMinTime:'06:00',
                slotMaxTime:'17:00',
                slotDuration:'1:00',
                dateClick: function(info) {
                    actionId=null;
                    showModalForm(info,'Registro de Curso')
                },
                eventClick: function(info) {
                    actionId=info.event.id;
                    showModalForm(info,'Editar programación')

                },
                events:'/api/horario'
                //events: 'https://fullcalendar.io/demo-events.json'
            });
            calendar.render();
        });
        let sendForm=function(){
            event.preventDefault();
            if(document.getElementById('form_to').value <= document.getElementById('form_from').value){
                toastr.error('Debe seleccionar una hora mayor a la de inicio');
                document.getElementById('form_to').focus();
                return;
            }
            let form= new FormData(document.getElementById('form_horario'));
            if(actionId){
                form.append('_method','put');
                request.post('/api/horario/'+actionId,form).then((response)=>{
                    toastr.success('Curso programado exitosamente');
                    calendar.refetchEvents();
                    document.getElementById('form_horario').reset()
                    formulario.hide();
                }).catch((error)=>{
                    toastr.success('Curso modificado exitosamente')
                });
            }else{
                request.post('/api/horario',form).then((response)=>{
                    toastr.success('Curso programado exitosamente');
                    calendar.refetchEvents();
                    document.getElementById('form_horario').reset()
                    formulario.hide();
                }).catch((error)=>{
                    toastr.success('Curso programado exitosamente')
                });
            }


        }

        let deleteHorario=function(){
            Swal.fire({
                text: "Una vez eliminado no se podra recuperar",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    request.delete('api/horario/'+actionId).then((response)=>{
                        toastr.success('Curso eliminado');
                        calendar.refetchEvents();
                        formulario.hide();
                    }).catch((error)=>{

                    });
                }
            })

        }
    </script>
@endsection
