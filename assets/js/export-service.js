window.exportAssignmentImage = function (groupName, topics, moduleCode, members, chapterName) {
    const container = document.createElement('div');
    container.style.position = 'fixed';
    container.style.left = '-9999px';
    container.style.top = '0';
    document.body.appendChild(container);

    const issueDate = new Date().toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric'
    });

    // Process topics into a bulleted vertical list
    const topicsHtml = topics.split('\n').filter(t => t.trim() !== "").map(t => `
        <div style="display: flex; align-items: flex-start; gap: 6px; margin-bottom: 3px;">
            <div style="width: 4px; height: 4px; background: #6366f1; border-radius: 50%; margin-top: 4px; flex-shrink: 0;"></div>
            <span style="font-size: 7px; color: #475569; line-height: 1.3;">${t.trim()}</span>
        </div>
    `).join('');

    const membersHtml = members.map(m => `
        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding: 3px 0;">
            <span style="font-size: 9px; font-weight: 700; color: #334155;">${m.name}</span>
            <span style="font-size: 8px; font-weight: 500; color: #94a3b8;">${m.phone}</span>
        </div>
    `).join('');

    container.innerHTML = `
        <div id="pdf-card" style="width: 380px; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; font-family: sans-serif;">
            
            <!-- HEADER -->
            <div style="background: #4f46e5; padding: 15px; text-align: center;">
                <div style="width: 44px; height: 44px; background: rgba(255,255,255,0.2); border-radius: 50%; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-graduation-cap" style="color: white; font-size: 18px; line-height: 1;"></i>
                </div>
                <h1 style="color: white; font-size: 14px; font-weight: 900; text-transform: uppercase; margin: 0;">Study Assignment</h1>
                <p style="color: #c7d2fe; font-size: 7px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-top: 3px;">Authorised Access Pass</p>
            </div>

            <div style="padding: 15px;">
                <!-- GROUP & DATE INFO -->
                <div style="display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 1px solid #f8fafc; padding-bottom: 10px; margin-bottom: 12px;">
                    <div>
                        <p style="font-size: 7px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin: 0;">Assigned Group</p>
                        <h2 style="font-size: 10px; font-weight: 800; color: #1e293b; margin: 0;">${groupName}</h2>
                    </div>
                    <div style="text-align: right;">
                        <p style="font-size: 7px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin: 0;">Issue Date</p>
                        <p style="font-size: 9px; font-weight: 700; color: #475569; margin: 0;">${issueDate}</p>
                    </div>
                </div>

                <!-- CHAPTER SECTION -->
                <div style="margin-bottom: 12px;">
                    <p style="font-size: 7px; font-weight: 700; color: #6366f1; text-transform: uppercase; margin: 0 0 2px 0;">Focus Chapter</p>
                    <h3 style="font-size: 7px; font-weight: 800; color: #0f172a; margin: 0;">${chapterName}</h3>
                </div>

                <!-- TOPICS (Bullet points) -->
                <div style="background: #f5f3ff; border: 1px solid #ede9fe; padding: 10px; border-radius: 6px; margin-bottom: 12px;">
                    <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 6px; border-bottom: 1px solid #ddd6fe; padding-bottom: 4px;">
                        <i class="fas fa-list-check" style="color: #6366f1; font-size: 9px;"></i>
                        <h3 style="font-size: 8px; font-weight: 900; color: #4f46e5; text-transform: uppercase; margin: 0;">Learning Objectives</h3>
                    </div>
                    <div style="display: flex; flex-direction: column;">
                        ${topicsHtml}
                    </div>
                </div>

                <!-- STUDENTS -->
                <div style="margin-bottom: 12px;">
                    <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 6px;">
                        <i class="fas fa-user-graduate" style="color: #94a3b8; font-size: 9px;"></i>
                        <h3 style="font-size: 8px; font-weight: 900; color: #64748b; text-transform: uppercase; margin: 0;">Assigned Students</h3>
                    </div>
                    <div>${membersHtml}</div>
                </div>

                <!-- FOOTER -->
                <div style="padding-top: 10px; border-top: 1px dashed #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <div style="font-size: 14px; color: #cbd5e1;"><i class="fas fa-barcode"></i></div>
                        <div>
                            <p style="font-size: 6px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin: 0;">Module Code</p>
                            <p style="font-size: 9px; font-weight: 900; color: #334155; margin: 0;">${moduleCode}</p>
                        </div>
                    </div>
                    <div style="background: #10b981; color: white; padding: 4px 10px; border-radius: 12px; display: flex; align-items: center; justify-content: center; gap: 6px; height: 20px;">
                        <i class="fas fa-check-circle" style="font-size: 9px;"></i>
                        <span style="font-size: 8px; font-weight: 900; text-transform: uppercase;">Authorised</span>
                    </div>
                </div>
                
                <p style="font-size: 6.5px; font-weight: 600; color: #94a3b8; margin-top: 20px; text-align: center; text-transform: uppercase; opacity: 0.7;">
                    System Developed by Thanseem (BIT - @UOM)
                </p>
            </div>
        </div>
    `;

    const opt = {
        margin: 10,
        filename: groupName.replace(/\s+/g, '_') + '_Assign.pdf',
        image: { type: 'jpeg', quality: 1 },
        html2canvas: { scale: 4, useCORS: true },
        jsPDF: { unit: 'px', format: [400, 550], orientation: 'portrait' }
    };

    html2pdf().set(opt).from(container.querySelector('#pdf-card')).save().then(() => {
        document.body.removeChild(container);
    });
};