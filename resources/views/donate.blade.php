@extends('layouts/app')

@section('title',__(Lang::get('web/app.donate')))

@section('content')

@push('custom_css')
<style>
    .loader{
        z-index: 1;
    }
    .loader .spinner-border{
        background: white;
    }
    .fs-dropdown {
        width: 94.3% !important;
    }
    .fs-label-wrap .fs-label {
        padding: 14px 22px 14px 16px !important;
    }
    .fs-label-wrap .fs-arrow {
        display: none;
    }

    .list-group-item.active {
        z-index: 2;
        color: #fff;
        background-color: #ffcd34;
        border-color: #ffcd34;
    }
</style>
@endpush

<div class="inner-banner-wrapper">
    <div class="container">
        <div class="step-form">
            <h4 class="inner-head inner-head-2">@lang('web/app.donate')</h4>
            <h5  style="text-align:center;padding-top: 50px;"></h5>
            @if(App::getLocale() == 'pt')
                <p>
                    Agradecemos suas orações e sua parceria enquanto nos preparamos para este evento único, histórico e transformador em novembro. Por favor, ore pelo GProCongresso II, e doe conforme a direção do Espírito. Cada oferta conta!
                </p><br> <p>
                    Sua doação nos ajudará a construir o reino de Deus equipando treinadores de pastores para treinar mais líderes pastorais e nos ajudará a atingir nossa meta de 100.000 novos treinadores de pastores e 1 milhão de líderes pastorais treinados até 2030.
                </p><br> <p>
                    RREACH é uma organização 501(c)(3). Todos os presentes são dedutíveis de impostos até o limite máximo permitido por lei. Não fornecemos bens ou serviços em consideração às suas contribuições.
                </p> <br> <p>
                    O RREACH é auditado anualmente por uma empresa independente de contabilidade pública. Descrições dos programas e atividades do RREACH e suas demonstrações financeiras auditadas estão disponíveis mediante solicitação. As contribuições são solicitadas com o entendimento de que o RREACH tem controle total sobre o uso de todos os fundos doados. RREACH é membro do Conselho Evangélico de Responsabilidade Financeira.

                </p>
            @elseif(App::getLocale() == 'sp')
                <p>
                    Agradecemos sus oraciones y su colaboración mientras nos preparamos  para este evento único, histórico y transformador en noviembre. Por favor, oren por el GProCongress II, y según el Espiritu los guien, los invitamos a dar también. ¡Cada aporte o contribución cuenta!
                </p><br> <p>
                    Su donación ayudará a edificar el reino de Dios al equipar capacitadores de pastores que formarán más líderes pastorales, y nos ayudará a alcanzar nuestra meta de 100.000 nuevos capacitadores de pastores, y 1 millón de líderes pastorales para el año 2030.
                </p><br> <p>
                    RREACH es una organización 501(c)(3). Todas las donaciones son deducibles de impuestos hasta el máximo permitido por la ley. No proveemos bienes o servicios a cambio de sus contribuciones.

                </p> <br> <p>
                    RREACH es auditada anualmente por una empresa de contabilidad pública independiente. Las descripciones de los programas y actividades de RREACH, así como sus estados financieros auditados, están disponibles previa solicitud. Las contribuciones se solicitan teniendo en cuenta que RREACH tiene pleno control sobre el uso de todos los fondos donados. RREACH es miembro del Consejo Evangélico para la Responsabilidad Financiera. 

                </p>
            @elseif(App::getLocale() == 'fr')
                <p>
                    Nous apprécions vos prières et votre partenariat pendant que nous nous préparons pour cet événement  unique, historique et transformateur en novembre. Merci de prier pour GProCongress II, et de faire un don selon que l’Esprit vous guide. Chaque don compte !
                </p><br> <p>
                    Votre don nous aidera à construire le royaume de Dieu en équipant des formateurs de pasteurs pour qu'ils forment davantage de leaders pastoraux, et il nous aidera à atteindre notre objectif de 100 000 nouveaux formateurs de pasteurs, et 1 million de leaders pastoraux supplémentaires formés, d’ici 2030.
                </p><br> <p>
                    RREACH est une organisation 501 (c) (3). Tous les dons sont déductibles d’impôt dans toute la mesure permise par la loi. Nous ne fournissons pas de biens ou de services en contrepartie de vos contributions.
                </p> <br> <p>
                    RREACH est auditée chaque année par un cabinet d’experts-comptables indépendant. Les descriptions des programmes et des activités de RREACH, ainsi que ses états financiers vérifiés, sont disponibles sur demande. Les contributions sont sollicitées en sachant que RREACH exerce un contrôle total sur l'utilisation de tous les fonds donnés. RREACH est membre du Conseil évangélique pour la redevabilité financière. 
                </p>
            @else
                <p>
                    We appreciate your prayers and your partnership as we prepare for this unique, historic and transformative event in November. Please pray for GProCongress II, and give as the Spirit leads. Every gift counts!
                </p><br> <p>
                    Your gift will help us to build God’s kingdom by equipping pastor trainers to train more pastoral leaders, and it will help us to reach our goal of 100,000 new pastor trainers, and 1 million more pastoral leaders trained, by 2030.
                </p><br> <p>
                    RREACH is a 501(c)(3) organization. All gifts are tax deductible to the full extent allowed by law. We do not provide goods or services in consideration for your contributions.
                </p> <br> <p>
                    RREACH is audited annually by an independent public accounting firm. Descriptions of RREACH’s programs and activities, and its audited financial statements, are available upon request. Contributions are solicited with the understanding that RREACH has complete control over the use of all donated funds. RREACH is a member of the Evangelical Council for Financial Accountability.

                </p>
            @endif
            <div class="col-lg-12 mt-5">
                <div class="step-next">
                    <a style="margin: 0 auto;" href="https://forms.ministryforms.net/embed.aspx?formId=3418d28f-f73c-4a01-9b8b-bdf44de3a1c1" class="main-btn" target="_blank">@lang('web/app.donate_now')</a>
                </div>
            </div>

        </div>
            
    </div>
</div>

@endsection



@push('custom_js')

    <script>

        $('.fSelect').fSelect({
			placeholder: 'Select some options',
			overflowText: '{n} selected',
			noResultsText: 'No results found',
			searchText: 'Search',
			showSearch: true
		});

    </script>

@endpush